import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
    Alert,
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
  warning: '#FF9800',
  background: '#F5F7FA',
  pending: '#FF9800',
  approved: '#4CAF50',
  rejected: '#F44336',
  cancelled: '#9E9E9E',
};

const MONTHS = [
  'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
  'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
];

export default function StudentLeaveApplicationsScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [leaveApplications, setLeaveApplications] = useState([]);
  const [error, setError] = useState('');
  const [selectedFilter, setSelectedFilter] = useState('all');

  useEffect(() => {
    fetchLeaveApplications();
  }, []);

  const fetchLeaveApplications = async () => {
    try {
      setLoading(true);
      setError('');

      const studentId = await AsyncStorage.getItem('student_id');
      
      if (!studentId) {
        Alert.alert('Error', 'Student ID not found. Please login again.');
        router.replace('/');
        return;
      }

      console.log('Fetching leave applications for student:', studentId);

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=st_leave_applications',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ student_id: studentId }),
        }
      );

      const result = await response.json();
      console.log('Leave applications response:', result);

      if (result.status === 'success' && result.data) {
        // Sort by date (newest first)
        const sortedData = result.data.sort((a, b) => 
          new Date(b.from_date) - new Date(a.from_date)
        );
        setLeaveApplications(sortedData);

        // Cache data
        await AsyncStorage.setItem('cached_leave_applications', JSON.stringify(sortedData));
      } else {
        setLeaveApplications([]);
        setError('No leave applications found');
      }
    } catch (err) {
      console.error('Error fetching leave applications:', err);
      setError('Failed to load leave applications. Please try again.');
      
      // Try to load cached data
      try {
        const cachedData = await AsyncStorage.getItem('cached_leave_applications');
        if (cachedData) {
          setLeaveApplications(JSON.parse(cachedData));
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
    fetchLeaveApplications();
  };

  const handleApplyLeave = () => {
    router.push('/StudentLeaveApply');
  };

  const handleBack = () => {
    router.back();
  };

  const getStatusColor = (status) => {
    switch (status?.toUpperCase()) {
      case 'APPROVED':
        return COLORS.approved;
      case 'PENDING':
        return COLORS.pending;
      case 'REJECTED':
        return COLORS.rejected;
      case 'CANCELLED':
        return COLORS.cancelled;
      default:
        return COLORS.pending;
    }
  };

  const getStatusIcon = (status) => {
    switch (status?.toUpperCase()) {
      case 'APPROVED':
        return 'checkmark-circle';
      case 'PENDING':
        return 'time';
      case 'REJECTED':
        return 'close-circle';
      case 'CANCELLED':
        return 'ban';
      default:
        return 'time';
    }
  };

  const getStatusLabel = (status) => {
    switch (status?.toUpperCase()) {
      case 'APPROVED':
        return 'Approved';
      case 'PENDING':
        return 'Pending';
      case 'REJECTED':
        return 'Rejected';
      case 'CANCELLED':
        return 'Cancelled';
      default:
        return status || 'Pending';
    }
  };

  const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const day = date.getDate();
    const month = MONTHS[date.getMonth()];
    const year = date.getFullYear();
    return `${day} ${month} ${year}`;
  };

  const formatShortDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const day = date.getDate();
    const month = MONTHS[date.getMonth()];
    return `${day} ${month}`;
  };

  const calculateDays = (fromDate, toDate) => {
    const from = new Date(fromDate);
    const to = new Date(toDate);
    const diffTime = Math.abs(to - from);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    return diffDays;
  };

  const isUpcoming = (fromDate) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const leaveDate = new Date(fromDate);
    return leaveDate >= today;
  };

  const isPast = (toDate) => {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const endDate = new Date(toDate);
    return endDate < today;
  };

  const stats = {
    total: leaveApplications.length,
    pending: leaveApplications.filter(l => l.status?.toUpperCase() === 'PENDING').length,
    approved: leaveApplications.filter(l => l.status?.toUpperCase() === 'APPROVED').length,
    rejected: leaveApplications.filter(l => l.status?.toUpperCase() === 'REJECTED').length,
  };

  const filteredApplications = leaveApplications.filter(leave => {
    if (selectedFilter === 'all') return true;
    return leave.status?.toUpperCase() === selectedFilter.toUpperCase();
  });

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <LinearGradient
          colors={[COLORS.primary, COLORS.secondary]}
          style={styles.loadingGradient}
        >
          <View style={styles.logoCircle}>
            <Ionicons name="calendar" size={40} color={COLORS.primary} />
          </View>
          <Text style={styles.loadingText}>Loading Leave Applications...</Text>
        </LinearGradient>
      </View>
    );
  }

  return (
    <View style={styles.container}>
      {/* Header */}
      <LinearGradient
        colors={[COLORS.primary, COLORS.secondary]}
        style={styles.header}
      >
        <View style={styles.headerTop}>
          {/* Back Button */}
          <TouchableOpacity 
            style={styles.backButton} 
            onPress={handleBack}
            activeOpacity={0.7}
          >
            <Ionicons name="arrow-back" size={24} color={COLORS.white} />
          </TouchableOpacity>

          <Text style={styles.headerTitle}>My Leave Applications</Text>

          {/* Apply Leave Button */}
          <TouchableOpacity 
            style={styles.addButton} 
            onPress={handleApplyLeave}
            activeOpacity={0.7}
          >
            <Ionicons name="add" size={28} color={COLORS.white} />
          </TouchableOpacity>
        </View>

        {/* Stats Cards */}
        <View style={styles.statsRow}>
          <TouchableOpacity 
            style={[
              styles.statCard, 
              selectedFilter === 'all' && styles.statCardActive
            ]}
            onPress={() => setSelectedFilter('all')}
          >
            <Ionicons name="documents" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.total}</Text>
            <Text style={styles.statLabel}>Total</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[
              styles.statCard,
              selectedFilter === 'pending' && styles.statCardActive
            ]}
            onPress={() => setSelectedFilter('pending')}
          >
            <View style={[styles.statDot, { backgroundColor: COLORS.pending }]} />
            <Text style={styles.statValue}>{stats.pending}</Text>
            <Text style={styles.statLabel}>Pending</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[
              styles.statCard,
              selectedFilter === 'approved' && styles.statCardActive
            ]}
            onPress={() => setSelectedFilter('approved')}
          >
            <View style={[styles.statDot, { backgroundColor: COLORS.approved }]} />
            <Text style={styles.statValue}>{stats.approved}</Text>
            <Text style={styles.statLabel}>Approved</Text>
          </TouchableOpacity>

          <TouchableOpacity 
            style={[
              styles.statCard,
              selectedFilter === 'rejected' && styles.statCardActive
            ]}
            onPress={() => setSelectedFilter('rejected')}
          >
            <View style={[styles.statDot, { backgroundColor: COLORS.rejected }]} />
            <Text style={styles.statValue}>{stats.rejected}</Text>
            <Text style={styles.statLabel}>Rejected</Text>
          </TouchableOpacity>
        </View>
      </LinearGradient>

      {/* Error Banner */}
      {error ? (
        <View style={styles.errorBanner}>
          <Ionicons name="information-circle" size={18} color="#F57C00" />
          <Text style={styles.errorBannerText}>{error}</Text>
          <TouchableOpacity onPress={fetchLeaveApplications}>
            <Ionicons name="refresh" size={18} color="#F57C00" />
          </TouchableOpacity>
        </View>
      ) : null}

      {/* Leave Applications List */}
      <ScrollView
        style={styles.scrollView}
        contentContainerStyle={styles.scrollContent}
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
        {filteredApplications.length === 0 ? (
          <View style={styles.emptyContainer}>
            <View style={styles.emptyIconContainer}>
              <Ionicons name="calendar-outline" size={80} color={COLORS.lightGray} />
            </View>
            <Text style={styles.emptyTitle}>
              {selectedFilter === 'all' ? 'No Leave Applications' : `No ${getStatusLabel(selectedFilter)} Applications`}
            </Text>
            <Text style={styles.emptyText}>
              {selectedFilter === 'all' 
                ? "You haven't applied for any leave yet.\nTap the + button to apply for leave."
                : `You don't have any ${selectedFilter.toLowerCase()} leave applications.`
              }
            </Text>
            {selectedFilter === 'all' && (
              <TouchableOpacity 
                style={styles.emptyButton}
                onPress={handleApplyLeave}
              >
                <Ionicons name="add-circle" size={20} color={COLORS.white} />
                <Text style={styles.emptyButtonText}>Apply for Leave</Text>
              </TouchableOpacity>
            )}
            {selectedFilter !== 'all' && (
              <TouchableOpacity 
                style={styles.clearFilterButton}
                onPress={() => setSelectedFilter('all')}
              >
                <Text style={styles.clearFilterText}>Show All Applications</Text>
              </TouchableOpacity>
            )}
          </View>
        ) : (
          <>
            <View style={styles.sectionHeader}>
              <Text style={styles.sectionTitle}>
                {selectedFilter === 'all' 
                  ? `All Applications (${filteredApplications.length})`
                  : `${getStatusLabel(selectedFilter)} Applications (${filteredApplications.length})`
                }
              </Text>
              {selectedFilter !== 'all' && (
                <TouchableOpacity onPress={() => setSelectedFilter('all')}>
                  <Text style={styles.clearFilter}>Clear Filter</Text>
                </TouchableOpacity>
              )}
            </View>
            
            {filteredApplications.map((leave, index) => (
              <LeaveCard
                key={leave.id || index}
                leave={leave}
                getStatusColor={getStatusColor}
                getStatusIcon={getStatusIcon}
                getStatusLabel={getStatusLabel}
                formatDate={formatDate}
                formatShortDate={formatShortDate}
                calculateDays={calculateDays}
                isUpcoming={isUpcoming}
                isPast={isPast}
              />
            ))}
          </>
        )}

        <View style={{ height: 100 }} />
      </ScrollView>

      {/* Floating Action Button */}
      <TouchableOpacity 
        style={styles.fab}
        onPress={handleApplyLeave}
        activeOpacity={0.8}
      >
        <LinearGradient
          colors={[COLORS.primary, COLORS.secondary]}
          style={styles.fabGradient}
        >
          <Ionicons name="add" size={30} color={COLORS.white} />
        </LinearGradient>
      </TouchableOpacity>
    </View>
  );
}

// Leave Card Component
function LeaveCard({ 
  leave, 
  getStatusColor, 
  getStatusIcon, 
  getStatusLabel, 
  formatDate,
  formatShortDate,
  calculateDays,
  isUpcoming,
  isPast
}) {
  const [expanded, setExpanded] = useState(false);
  const statusColor = getStatusColor(leave.status);
  const days = calculateDays(leave.from_date, leave.to_date);
  const upcoming = isUpcoming(leave.from_date);
  const past = isPast(leave.to_date);

  return (
    <TouchableOpacity
      style={styles.leaveCard}
      onPress={() => setExpanded(!expanded)}
      activeOpacity={0.7}
    >
      {/* Status Bar */}
      <View style={[styles.statusBar, { backgroundColor: statusColor }]} />

      <View style={styles.cardContent}>
        {/* Date and Status Row */}
        <View style={styles.cardHeader}>
          {/* Date Display */}
          <View style={styles.dateContainer}>
            <View style={[styles.dateBox, { borderColor: statusColor }]}>
              <Text style={[styles.dateDay, { color: statusColor }]}>
                {new Date(leave.from_date).getDate()}
              </Text>
              <Text style={styles.dateMonth}>
                {MONTHS[new Date(leave.from_date).getMonth()]}
              </Text>
            </View>
            
            <View style={styles.dateArrow}>
              <Ionicons name="arrow-forward" size={16} color={COLORS.gray} />
              <Text style={styles.daysCount}>{days} {days === 1 ? 'Day' : 'Days'}</Text>
            </View>

            <View style={[styles.dateBox, { borderColor: statusColor }]}>
              <Text style={[styles.dateDay, { color: statusColor }]}>
                {new Date(leave.to_date).getDate()}
              </Text>
              <Text style={styles.dateMonth}>
                {MONTHS[new Date(leave.to_date).getMonth()]}
              </Text>
            </View>
          </View>

          {/* Status Badge */}
          <View style={styles.statusContainer}>
            <View style={[styles.statusBadge, { backgroundColor: statusColor + '15' }]}>
              <Ionicons 
                name={getStatusIcon(leave.status)} 
                size={16} 
                color={statusColor} 
              />
              <Text style={[styles.statusText, { color: statusColor }]}>
                {getStatusLabel(leave.status)}
              </Text>
            </View>
            
            {/* Timeline Badge */}
            {leave.status?.toUpperCase() === 'APPROVED' && (
              <View style={[
                styles.timelineBadge,
                { backgroundColor: upcoming ? '#E3F2FD' : past ? '#FFEBEE' : '#E8F5E9' }
              ]}>
                <Ionicons 
                  name={upcoming ? 'time-outline' : past ? 'checkmark-done' : 'today'} 
                  size={12} 
                  color={upcoming ? '#1976D2' : past ? '#C62828' : '#2E7D32'} 
                />
                <Text style={[
                  styles.timelineText,
                  { color: upcoming ? '#1976D2' : past ? '#C62828' : '#2E7D32' }
                ]}>
                  {upcoming ? 'Upcoming' : past ? 'Completed' : 'Ongoing'}
                </Text>
              </View>
            )}
          </View>
        </View>

        {/* Leave ID */}
        <View style={styles.leaveIdRow}>
          <Ionicons name="document-text-outline" size={14} color={COLORS.gray} />
          <Text style={styles.leaveId}>Application #{leave.id}</Text>
        </View>

        {/* Cause/Reason */}
        <View style={styles.causeContainer}>
          <Text style={styles.causeLabel}>Reason:</Text>
          <Text 
            style={styles.causeText}
            numberOfLines={expanded ? undefined : 2}
          >
            {leave.cause || 'No reason provided'}
          </Text>
        </View>

        {/* Expand/Collapse */}
        {leave.cause && leave.cause.length > 80 && (
          <View style={styles.expandRow}>
            <Text style={styles.expandText}>
              {expanded ? 'Show Less' : 'Show More'}
            </Text>
            <Ionicons 
              name={expanded ? 'chevron-up' : 'chevron-down'} 
              size={16} 
              color={COLORS.secondary} 
            />
          </View>
        )}

        {/* Expanded Details */}
        {expanded && (
          <View style={styles.expandedDetails}>
            <View style={styles.detailRow}>
              <View style={styles.detailItem}>
                <Ionicons name="calendar-outline" size={16} color={COLORS.gray} />
                <View>
                  <Text style={styles.detailLabel}>From Date</Text>
                  <Text style={styles.detailValue}>{formatDate(leave.from_date)}</Text>
                </View>
              </View>
              <View style={styles.detailItem}>
                <Ionicons name="calendar" size={16} color={COLORS.gray} />
                <View>
                  <Text style={styles.detailLabel}>To Date</Text>
                  <Text style={styles.detailValue}>{formatDate(leave.to_date)}</Text>
                </View>
              </View>
            </View>
            
            <View style={styles.totalDaysRow}>
              <View style={[styles.totalDaysBadge, { backgroundColor: statusColor + '15' }]}>
                <Ionicons name="hourglass-outline" size={16} color={statusColor} />
                <Text style={[styles.totalDaysText, { color: statusColor }]}>
                  Total Duration: {days} {days === 1 ? 'Day' : 'Days'}
                </Text>
              </View>
            </View>
          </View>
        )}
      </View>
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
  },
  loadingGradient: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  logoCircle: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: COLORS.white,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 8,
  },
  loadingText: {
    fontSize: 18,
    fontWeight: '600',
    color: COLORS.white,
  },
  header: {
    paddingTop: 60,
    paddingBottom: 20,
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
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  addButton: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  statsRow: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    gap: 8,
  },
  statCard: {
    flex: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    borderRadius: 12,
    padding: 12,
    alignItems: 'center',
  },
  statCardActive: {
    backgroundColor: 'rgba(255, 255, 255, 0.4)',
    borderWidth: 2,
    borderColor: COLORS.accent,
  },
  statDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    marginBottom: 4,
  },
  statValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.white,
    marginTop: 4,
  },
  statLabel: {
    fontSize: 10,
    color: COLORS.white,
    opacity: 0.9,
    marginTop: 2,
  },
  errorBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFF3E0',
    padding: 12,
    marginHorizontal: 20,
    marginTop: 15,
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
  },
  scrollContent: {
    padding: 20,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 15,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  clearFilter: {
    fontSize: 13,
    color: COLORS.secondary,
    fontWeight: '600',
  },
  emptyContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyIconContainer: {
    width: 140,
    height: 140,
    borderRadius: 70,
    backgroundColor: COLORS.white,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 24,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 4,
  },
  emptyTitle: {
    fontSize: 22,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 10,
  },
  emptyText: {
    fontSize: 14,
    color: COLORS.gray,
    textAlign: 'center',
    lineHeight: 22,
    marginBottom: 24,
  },
  emptyButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.secondary,
    paddingVertical: 14,
    paddingHorizontal: 24,
    borderRadius: 25,
    gap: 8,
    shadowColor: COLORS.secondary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  emptyButtonText: {
    fontSize: 16,
    fontWeight: '600',
    color: COLORS.white,
  },
  clearFilterButton: {
    paddingVertical: 12,
    paddingHorizontal: 20,
  },
  clearFilterText: {
    fontSize: 14,
    color: COLORS.secondary,
    fontWeight: '600',
  },
  leaveCard: {
    backgroundColor: COLORS.white,
    borderRadius: 16,
    marginBottom: 15,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  statusBar: {
    height: 4,
  },
  cardContent: {
    padding: 16,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  dateContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  dateBox: {
    width: 50,
    height: 55,
    borderRadius: 10,
    borderWidth: 2,
    backgroundColor: COLORS.background,
    justifyContent: 'center',
    alignItems: 'center',
  },
  dateDay: {
    fontSize: 20,
    fontWeight: 'bold',
  },
  dateMonth: {
    fontSize: 11,
    color: COLORS.gray,
    fontWeight: '600',
  },
  dateArrow: {
    alignItems: 'center',
  },
  daysCount: {
    fontSize: 10,
    color: COLORS.gray,
    marginTop: 2,
  },
  statusContainer: {
    alignItems: 'flex-end',
    gap: 6,
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 20,
    gap: 4,
  },
  statusText: {
    fontSize: 12,
    fontWeight: '700',
  },
  timelineBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 12,
    gap: 4,
  },
  timelineText: {
    fontSize: 10,
    fontWeight: '600',
  },
  leaveIdRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 12,
  },
  leaveId: {
    fontSize: 12,
    color: COLORS.gray,
  },
  causeContainer: {
    backgroundColor: COLORS.background,
    padding: 12,
    borderRadius: 10,
  },
  causeLabel: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 6,
  },
  causeText: {
    fontSize: 14,
    color: COLORS.gray,
    lineHeight: 22,
  },
  expandRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
    paddingTop: 12,
  },
  expandText: {
    fontSize: 13,
    color: COLORS.secondary,
    fontWeight: '600',
  },
  expandedDetails: {
    marginTop: 16,
    paddingTop: 16,
    borderTopWidth: 1,
    borderTopColor: COLORS.lightGray,
  },
  detailRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  detailItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
    flex: 1,
  },
  detailLabel: {
    fontSize: 11,
    color: COLORS.gray,
  },
  detailValue: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.primary,
  },
  totalDaysRow: {
    alignItems: 'center',
  },
  totalDaysBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 25,
    gap: 8,
  },
  totalDaysText: {
    fontSize: 14,
    fontWeight: '700',
  },
  fab: {
    position: 'absolute',
    bottom: 30,
    right: 20,
    shadowColor: COLORS.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 8,
  },
  fabGradient: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
  },
});