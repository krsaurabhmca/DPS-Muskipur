import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
    Dimensions,
    RefreshControl,
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View
} from 'react-native';

const { width } = Dimensions.get('window');

const COLORS = {
  primary: '#1B5E20',
  secondary: '#4CAF50',
  accent: '#FFC107',
  white: '#FFFFFF',
  gray: '#757575',
  lightGray: '#E0E0E0',
  error: '#F44336',
  success: '#4CAF50',
  background: '#F5F7FA',
  paid: '#4CAF50',
  pending: '#FFC107',
  overdue: '#F44336',
};

export default function PaymentHistoryScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [payments, setPayments] = useState([]);
  const [error, setError] = useState('');
  const [stats, setStats] = useState({
    totalPaid: 0,
    totalPayments: 0,
    currentDues: 0,
  });
  const [selectedFilter, setSelectedFilter] = useState('all'); // all, paid, pending

  useEffect(() => {
    fetchPaymentHistory();
  }, []);

  useEffect(() => {
    calculateStats();
  }, [payments]);

  const fetchPaymentHistory = async () => {
    try {
      setLoading(true);
      setError('');

      const studentId = await AsyncStorage.getItem('student_id');
      
      if (!studentId) {
        router.replace('/index');
        return;
      }

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=payment_history',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ student_id: studentId }),
        }
      );

      const result = await response.json();
      
      if (result.status === 'success' && result.data && Array.isArray(result.data)) {
        // Sort by date (newest first)
        const sortedPayments = result.data.sort((a, b) => 
          new Date(b.paid_date) - new Date(a.paid_date)
        );
        setPayments(sortedPayments);
        
        // Cache payments
        await AsyncStorage.setItem('cached_payments', JSON.stringify(sortedPayments));
      } else {
        setError('No payment history found');
        setPayments([]);
      }
    } catch (err) {
      console.error('Error fetching payment history:', err);
      setError('Failed to load payment history');
      
      // Try to load cached payments
      try {
        const cachedPayments = await AsyncStorage.getItem('cached_payments');
        if (cachedPayments) {
          setPayments(JSON.parse(cachedPayments));
          setError('Showing cached data. Network error occurred.');
        }
      } catch (cacheErr) {
        console.error('Cache error:', cacheErr);
      }
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  const onRefresh = () => {
    setRefreshing(true);
    fetchPaymentHistory();
  };

  const calculateStats = () => {
    const totalPaid = payments.reduce((sum, payment) => 
      sum + parseFloat(payment.paid_amount || 0), 0
    );
    
    const currentDues = payments.reduce((sum, payment) => 
      sum + parseFloat(payment.current_dues || 0), 0
    );

    setStats({
      totalPaid: totalPaid,
      totalPayments: payments.length,
      currentDues: currentDues,
    });
  };

  const formatCurrency = (amount) => {
    return `₹${parseFloat(amount || 0).toLocaleString('en-IN', {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    })}`;
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = { day: '2-digit', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('en-GB', options);
  };

  const getStatusColor = (status) => {
    switch (status?.toUpperCase()) {
      case 'PAID':
        return COLORS.paid;
      case 'PENDING':
        return COLORS.pending;
      case 'OVERDUE':
        return COLORS.overdue;
      default:
        return COLORS.gray;
    }
  };

  const getFilteredPayments = () => {
    if (selectedFilter === 'all') {
      return payments;
    }
    return payments.filter(p => p.status?.toLowerCase() === selectedFilter);
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <View style={styles.logoCircle}>
          <Ionicons name="receipt" size={40} color={COLORS.primary} />
        </View>
        <Text style={styles.loadingText}>Loading payment history...</Text>
      </View>
    );
  }

  const filteredPayments = getFilteredPayments();

  return (
    <View style={styles.container}>
      {/* Header */}
      <LinearGradient
        colors={[COLORS.primary, COLORS.secondary]}
        style={styles.header}
      >
        <View style={styles.headerTop}>
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => router.back()}
          >
            <Ionicons name="arrow-back" size={24} color={COLORS.white} />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Payment History</Text>
          <View style={{ width: 40 }} />
        </View>

        {/* Stats Cards */}
        <View style={styles.statsContainer}>
          <View style={styles.statCard}>
            <Ionicons name="wallet" size={24} color={COLORS.white} />
            <Text style={styles.statValue}>{formatCurrency(stats.totalPaid)}</Text>
            <Text style={styles.statLabel}>Total Paid</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statCard}>
            <Ionicons name="receipt" size={24} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.totalPayments}</Text>
            <Text style={styles.statLabel}>Payments</Text>
          </View>
        </View>

        {/* Dues Card */}
        {stats.currentDues > 0 && (
          <View style={styles.duesCard}>
            <View style={styles.duesIcon}>
              <Ionicons name="alert-circle" size={24} color={COLORS.accent} />
            </View>
            <View style={styles.duesContent}>
              <Text style={styles.duesLabel}>Current Dues</Text>
              <Text style={styles.duesAmount}>{formatCurrency(stats.currentDues)}</Text>
            </View>
          </View>
        )}
      </LinearGradient>

      {/* Filter Buttons */}
      <View style={styles.filterContainer}>
        <FilterButton
          title="All"
          active={selectedFilter === 'all'}
          onPress={() => setSelectedFilter('all')}
          count={payments.length}
        />
        <FilterButton
          title="Paid"
          active={selectedFilter === 'paid'}
          onPress={() => setSelectedFilter('paid')}
          count={payments.filter(p => p.status?.toLowerCase() === 'paid').length}
        />
        <FilterButton
          title="Pending"
          active={selectedFilter === 'pending'}
          onPress={() => setSelectedFilter('pending')}
          count={payments.filter(p => p.status?.toLowerCase() === 'pending').length}
        />
      </View>

      {/* Error Banner */}
      {error && (
        <View style={styles.errorBanner}>
          <Ionicons name="information-circle" size={16} color="#F57C00" />
          <Text style={styles.errorBannerText}>{error}</Text>
        </View>
      )}

      {/* Payment List */}
      <ScrollView
        style={styles.scrollView}
        showsVerticalScrollIndicator={false}
        refreshControl={
          <RefreshControl 
            refreshing={refreshing} 
            onRefresh={onRefresh}
            colors={[COLORS.secondary]}
            tintColor={COLORS.secondary}
          />
        }
      >
        {filteredPayments.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="receipt-outline" size={80} color={COLORS.lightGray} />
            <Text style={styles.emptyText}>
              {selectedFilter === 'all' 
                ? 'No payment history found' 
                : `No ${selectedFilter} payments`}
            </Text>
          </View>
        ) : (
          filteredPayments.map((payment, index) => (
            <PaymentCard
              key={payment.receipt_id}
              payment={payment}
              index={index}
              formatCurrency={formatCurrency}
              formatDate={formatDate}
              getStatusColor={getStatusColor}
            />
          ))
        )}
      </ScrollView>
    </View>
  );
}

function FilterButton({ title, active, onPress, count }) {
  return (
    <TouchableOpacity
      style={[styles.filterButton, active && styles.filterButtonActive]}
      onPress={onPress}
    >
      <Text style={[styles.filterButtonText, active && styles.filterButtonTextActive]}>
        {title}
      </Text>
      {count > 0 && (
        <View style={[styles.countBadge, active && styles.countBadgeActive]}>
          <Text style={[styles.countText, active && styles.countTextActive]}>
            {count}
          </Text>
        </View>
      )}
    </TouchableOpacity>
  );
}

function PaymentCard({ 
  payment, 
  index, 
  formatCurrency, 
  formatDate, 
  getStatusColor
}) {
  const [expanded, setExpanded] = useState(false);

  const getFeeBreakdown = () => {
    const fees = [];
    
    if (parseFloat(payment.admission_fee || 0) > 0) {
      fees.push({ label: 'Admission Fee', amount: payment.admission_fee });
    }
    if (parseFloat(payment.tution_fee || 0) > 0) {
      fees.push({ label: 'Tuition Fee', amount: payment.tution_fee });
    }
    if (parseFloat(payment.development_fee || 0) > 0) {
      fees.push({ label: 'Development Fee', amount: payment.development_fee });
    }
    if (parseFloat(payment.annual_fee || 0) > 0) {
      fees.push({ label: 'Annual Fee', amount: payment.annual_fee });
    }
    if (parseFloat(payment.registration_fee || 0) > 0) {
      fees.push({ label: 'Registration Fee', amount: payment.registration_fee });
    }
    if (parseFloat(payment.hostel_fee || 0) > 0) {
      fees.push({ label: 'Hostel Fee', amount: payment.hostel_fee });
    }
    if (parseFloat(payment.transport_fee || 0) > 0) {
      fees.push({ label: 'Transport Fee', amount: payment.transport_fee });
    }
    if (parseFloat(payment.other_fee || 0) > 0) {
      fees.push({ label: 'Other Fee', amount: payment.other_fee });
    }
    
    return fees;
  };

  const feeBreakdown = getFeeBreakdown();
  const statusColor = getStatusColor(payment.status);

  return (
    <TouchableOpacity
      style={styles.paymentCard}
      activeOpacity={0.7}
      onPress={() => setExpanded(!expanded)}
    >
      {/* Receipt Badge */}
      <View style={[styles.receiptBadge, { backgroundColor: statusColor }]}>
        <Text style={styles.receiptBadgeText}>#{payment.receipt_id}</Text>
      </View>

      {/* Card Header */}
      <View style={styles.paymentHeader}>
        <View style={[styles.paymentIcon, { backgroundColor: statusColor + '20' }]}>
          <Ionicons name="receipt" size={28} color={statusColor} />
        </View>
        
        <View style={styles.paymentInfo}>
          <Text style={styles.paymentMonth}>{payment.paid_month}</Text>
          <Text style={styles.paymentDate}>
            <Ionicons name="calendar-outline" size={14} color={COLORS.gray} />
            {' '}{formatDate(payment.paid_date)}
          </Text>
        </View>

        <View style={[styles.statusBadge, { backgroundColor: statusColor + '15' }]}>
          <Text style={[styles.statusText, { color: statusColor }]}>
            {payment.status}
          </Text>
        </View>
      </View>

      {/* Amount Section */}
      <View style={styles.amountSection}>
        <View style={styles.amountRow}>
          <Text style={styles.amountLabel}>Total Amount:</Text>
          <Text style={styles.amountValue}>{formatCurrency(payment.total)}</Text>
        </View>
        <View style={styles.amountRow}>
          <Text style={styles.amountLabel}>Paid Amount:</Text>
          <Text style={[styles.amountValue, { color: COLORS.success }]}>
            {formatCurrency(payment.paid_amount)}
          </Text>
        </View>
        {parseFloat(payment.current_dues || 0) > 0 && (
          <View style={styles.amountRow}>
            <Text style={styles.amountLabel}>Current Dues:</Text>
            <Text style={[styles.amountValue, { color: COLORS.error }]}>
              {formatCurrency(payment.current_dues)}
            </Text>
          </View>
        )}
      </View>

      {/* Expandable Fee Breakdown */}
      {expanded && feeBreakdown.length > 0 && (
        <View style={styles.feeBreakdown}>
          <View style={styles.breakdownHeader}>
            <Ionicons name="list" size={18} color={COLORS.primary} />
            <Text style={styles.breakdownTitle}>Fee Breakdown</Text>
          </View>
          {feeBreakdown.map((fee, idx) => (
            <View key={idx} style={styles.feeRow}>
              <View style={styles.feeDot} />
              <Text style={styles.feeLabel}>{fee.label}</Text>
              <Text style={styles.feeAmount}>{formatCurrency(fee.amount)}</Text>
            </View>
          ))}
        </View>
      )}

      {/* Remarks */}
      {expanded && payment.remarks && (
        <View style={styles.remarksSection}>
          <Text style={styles.remarksLabel}>Remarks:</Text>
          <Text style={styles.remarksText}>{payment.remarks}</Text>
        </View>
      )}

      {/* Action Button */}
      <TouchableOpacity
        style={styles.expandButton}
        onPress={() => setExpanded(!expanded)}
      >
        <Ionicons 
          name={expanded ? 'chevron-up' : 'chevron-down'} 
          size={20} 
          color={COLORS.secondary} 
        />
        <Text style={styles.expandButtonText}>
          {expanded ? 'Show Less' : 'View Details'}
        </Text>
      </TouchableOpacity>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.background,
  },
  logoCircle: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: COLORS.accent,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 4,
    borderColor: COLORS.primary,
  },
  loadingText: {
    marginTop: 20,
    fontSize: 16,
    color: COLORS.primary,
    fontWeight: '600',
  },
  header: {
    paddingTop: 60,
    paddingBottom: 25,
    borderBottomLeftRadius: 30,
    borderBottomRightRadius: 30,
  },
  headerTop: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 20,
    marginBottom: 20,
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  statsContainer: {
    flexDirection: 'row',
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    marginHorizontal: 20,
    marginBottom: 15,
    borderRadius: 15,
    padding: 15,
  },
  statCard: {
    flex: 1,
    alignItems: 'center',
    gap: 6,
  },
  statDivider: {
    width: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
  },
  statValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  statLabel: {
    fontSize: 12,
    color: COLORS.white,
    opacity: 0.9,
  },
  duesCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 193, 7, 0.2)',
    marginHorizontal: 20,
    padding: 15,
    borderRadius: 15,
    gap: 12,
  },
  duesIcon: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  duesContent: {
    flex: 1,
  },
  duesLabel: {
    fontSize: 13,
    color: COLORS.white,
    opacity: 0.95,
  },
  duesAmount: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.white,
    marginTop: 2,
  },
  filterContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingVertical: 15,
    gap: 10,
  },
  filterButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: 12,
    backgroundColor: COLORS.white,
    gap: 6,
  },
  filterButtonActive: {
    backgroundColor: COLORS.secondary,
  },
  filterButtonText: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.gray,
  },
  filterButtonTextActive: {
    color: COLORS.white,
  },
  countBadge: {
    backgroundColor: COLORS.lightGray,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 10,
  },
  countBadgeActive: {
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
  },
  countText: {
    fontSize: 11,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  countTextActive: {
    color: COLORS.white,
  },
  errorBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFF3E0',
    padding: 12,
    marginHorizontal: 20,
    marginBottom: 10,
    borderRadius: 10,
    gap: 10,
  },
  errorBannerText: {
    flex: 1,
    fontSize: 13,
    color: '#F57C00',
  },
  scrollView: {
    flex: 1,
    paddingHorizontal: 20,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 80,
  },
  emptyText: {
    fontSize: 16,
    color: COLORS.gray,
    marginTop: 20,
  },
  paymentCard: {
    backgroundColor: COLORS.white,
    borderRadius: 20,
    marginBottom: 15,
    overflow: 'hidden',
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  receiptBadge: {
    paddingHorizontal: 15,
    paddingVertical: 8,
  },
  receiptBadgeText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  paymentHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 15,
    paddingTop: 10,
  },
  paymentIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  paymentInfo: {
    flex: 1,
  },
  paymentMonth: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 4,
  },
  paymentDate: {
    fontSize: 13,
    color: COLORS.gray,
  },
  statusBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  statusText: {
    fontSize: 12,
    fontWeight: 'bold',
  },
  amountSection: {
    paddingHorizontal: 15,
    paddingBottom: 15,
    gap: 8,
  },
  amountRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  amountLabel: {
    fontSize: 14,
    color: COLORS.gray,
  },
  amountValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  feeBreakdown: {
    backgroundColor: COLORS.background,
    padding: 15,
    marginHorizontal: 15,
    marginBottom: 15,
    borderRadius: 12,
  },
  breakdownHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
    gap: 8,
  },
  breakdownTitle: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  feeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 6,
  },
  feeDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: COLORS.secondary,
    marginRight: 10,
  },
  feeLabel: {
    flex: 1,
    fontSize: 13,
    color: COLORS.gray,
  },
  feeAmount: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.primary,
  },
  remarksSection: {
    backgroundColor: '#FFF3E0',
    padding: 15,
    marginHorizontal: 15,
    marginBottom: 15,
    borderRadius: 12,
  },
  remarksLabel: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#F57C00',
    marginBottom: 4,
  },
  remarksText: {
    fontSize: 13,
    color: COLORS.gray,
    lineHeight: 18,
  },
  expandButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 14,
    gap: 6,
    borderTopWidth: 1,
    borderTopColor: COLORS.lightGray + '50',
  },
  expandButtonText: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.secondary,
  },
});