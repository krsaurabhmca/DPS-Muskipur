import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
  Alert,
  Modal,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View
} from 'react-native';

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
  pending: '#FFC107',
  approved: '#4CAF50',
  rejected: '#F44336',
};

export default async function AdminLeaveListScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [leaves, setLeaves] = useState([]);
  const [filteredLeaves, setFilteredLeaves] = useState([]);
  const [error, setError] = useState('');
  const [selectedFilter, setSelectedFilter] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [modalVisible, setModalVisible] = useState(false);
  const [selectedLeave, setSelectedLeave] = useState(null);
  const [remarks, setRemarks] = useState('');

  useEffect(() => {
    checkAdminAccess();
  }, []);

  useEffect(() => {
    filterLeaves();
  }, [selectedFilter, searchQuery, leaves]);
  const userId = await AsyncStorage.getItem('user_id');
  const checkAdminAccess = async () => {
    const userType = await AsyncStorage.getItem('user_type');
    if (userType !== 'ADMIN') {
      Alert.alert('Access Denied', 'This section is only for administrators.');
      router.back();
      return;
    }
    fetchLeaveApplications();
  };

  const fetchLeaveApplications = async () => {
    try {
      setLoading(true);
      setError('');

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=leave_applied',
        {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        }
      );

      const result = await response.json();
      
      if (result.status === 'success' && result.data && Array.isArray(result.data)) {
        // Sort by date (newest first)
        const sortedLeaves = result.data.sort((a, b) => 
          new Date(b.from_date) - new Date(a.from_date)
        );
        setLeaves(sortedLeaves);
        
        // Cache data
        await AsyncStorage.setItem('cached_admin_leaves', JSON.stringify(sortedLeaves));
      } else {
        setError('No leave applications found');
        setLeaves([]);
      }
    } catch (err) {
      console.error('Error fetching leaves:', err);
      setError('Failed to load leave applications');
      
      // Try to load cached data
      try {
        const cachedData = await AsyncStorage.getItem('cached_admin_leaves');
        if (cachedData) {
          setLeaves(JSON.parse(cachedData));
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

  const filterLeaves = () => {
    let filtered = leaves;

    // Apply status filter
    if (selectedFilter !== 'all') {
      filtered = filtered.filter(leave => 
        (leave.status || 'PENDING').toLowerCase() === selectedFilter.toLowerCase()
      );
    }

    // Apply search filter
    if (searchQuery.trim()) {
      filtered = filtered.filter(leave =>
        leave.student_id.toLowerCase().includes(searchQuery.toLowerCase()) ||
        leave.cause.toLowerCase().includes(searchQuery.toLowerCase()) ||
        (leave.student_name && leave.student_name.toLowerCase().includes(searchQuery.toLowerCase()))
      );
    }

    setFilteredLeaves(filtered);
  };

  const onRefresh = () => {
    setRefreshing(true);
    fetchLeaveApplications();
  };

  const handleStatusUpdate = async (leaveId, newStatus) => {
    try {
      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=leave_update',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            id: leaveId,
            status: newStatus,
            remarks: remarks || '',
            updated_by : userId,
          }),
        }
      );

      const result = await response.json();
      
      if (result.status === 'success') {
        Alert.alert('Success', `Leave ${newStatus.toLowerCase()} successfully!`);
        setModalVisible(false);
        setRemarks('');
        setSelectedLeave(null);
        fetchLeaveApplications();
      } else {
        Alert.alert('Error', result.msg || 'Failed to update leave status');
      }
    } catch (err) {
      console.error('Error updating leave:', err);
      Alert.alert('Error', 'Network error occurred');
    }
  };

  const openUpdateModal = (leave, action) => {
    setSelectedLeave(leave);
    setModalVisible(true);
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = { day: '2-digit', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('en-GB', options);
  };

  const calculateDays = (fromDate, toDate) => {
    const from = new Date(fromDate);
    const to = new Date(toDate);
    const diffTime = Math.abs(to - from);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    return diffDays;
  };

  const getStatusColor = (status) => {
    switch (status?.toUpperCase()) {
      case 'APPROVED': return COLORS.approved;
      case 'REJECTED': return COLORS.rejected;
      case 'PENDING': return COLORS.pending;
      default: return COLORS.pending;
    }
  };

  const getStatusIcon = (status) => {
    switch (status?.toUpperCase()) {
      case 'APPROVED': return 'checkmark-circle';
      case 'REJECTED': return 'close-circle';
      case 'PENDING': return 'time';
      default: return 'time';
    }
  };

  const stats = {
    total: leaves.length,
    pending: leaves.filter(l => !l.status || l.status === 'PENDING').length,
    approved: leaves.filter(l => l.status === 'APPROVED').length,
    rejected: leaves.filter(l => l.status === 'REJECTED').length,
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <View style={styles.logoCircle}>
          <Ionicons name="calendar" size={40} color={COLORS.primary} />
        </View>
        <Text style={styles.loadingText}>Loading applications...</Text>
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
          <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
            <Ionicons name="arrow-back" size={24} color={COLORS.white} />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Leave Applications</Text>
          <TouchableOpacity style={styles.refreshButton} onPress={onRefresh}>
            <Ionicons name="refresh" size={24} color={COLORS.white} />
          </TouchableOpacity>
        </View>

        {/* Stats */}
        <View style={styles.statsRow}>
          <View style={styles.statCard}>
            <Ionicons name="documents" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.total}</Text>
            <Text style={styles.statLabel}>Total</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="time" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.pending}</Text>
            <Text style={styles.statLabel}>Pending</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="checkmark-circle" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.approved}</Text>
            <Text style={styles.statLabel}>Approved</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="close-circle" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.rejected}</Text>
            <Text style={styles.statLabel}>Rejected</Text>
          </View>
        </View>

        {/* Search Bar */}
        <View style={styles.searchContainer}>
          <Ionicons name="search" size={20} color={COLORS.gray} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search by student ID or reason..."
            placeholderTextColor={COLORS.gray}
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
          {searchQuery ? (
            <TouchableOpacity onPress={() => setSearchQuery('')}>
              <Ionicons name="close-circle" size={20} color={COLORS.gray} />
            </TouchableOpacity>
          ) : null}
        </View>
      </LinearGradient>

      {/* Filter Buttons */}
      <View style={styles.filterContainer}>
        <FilterButton
          title="All"
          active={selectedFilter === 'all'}
          onPress={() => setSelectedFilter('all')}
          count={leaves.length}
        />
        <FilterButton
          title="Pending"
          active={selectedFilter === 'pending'}
          onPress={() => setSelectedFilter('pending')}
          count={stats.pending}
        />
        <FilterButton
          title="Approved"
          active={selectedFilter === 'approved'}
          onPress={() => setSelectedFilter('approved')}
          count={stats.approved}
        />
        <FilterButton
          title="Rejected"
          active={selectedFilter === 'rejected'}
          onPress={() => setSelectedFilter('rejected')}
          count={stats.rejected}
        />
      </View>

      {/* Error Banner */}
      {error && (
        <View style={styles.errorBanner}>
          <Ionicons name="information-circle" size={16} color="#F57C00" />
          <Text style={styles.errorBannerText}>{error}</Text>
        </View>
      )}

      {/* Leave List */}
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
        {filteredLeaves.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="document-text-outline" size={80} color={COLORS.lightGray} />
            <Text style={styles.emptyText}>
              {searchQuery ? 'No matching applications' : 'No leave applications'}
            </Text>
          </View>
        ) : (
          filteredLeaves.map((leave) => (
            <LeaveCard
              key={leave.id}
              leave={leave}
              formatDate={formatDate}
              calculateDays={calculateDays}
              getStatusColor={getStatusColor}
              getStatusIcon={getStatusIcon}
              openUpdateModal={openUpdateModal}
            />
          ))
        )}
      </ScrollView>

      {/* Update Modal */}
      <Modal
        visible={modalVisible}
        transparent={true}
        animationType="slide"
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalContainer}>
          <TouchableOpacity 
            style={styles.modalBackdrop}
            activeOpacity={1}
            onPress={() => setModalVisible(false)}
          />
          <View style={styles.modalContent}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Update Leave Status</Text>
              <TouchableOpacity onPress={() => setModalVisible(false)}>
                <Ionicons name="close" size={28} color={COLORS.gray} />
              </TouchableOpacity>
            </View>

            {selectedLeave && (
              <View style={styles.modalBody}>
                <View style={styles.leaveInfoModal}>
                  <Text style={styles.modalLabel}>Student ID:</Text>
                  <Text style={styles.modalValue}>{selectedLeave.student_id}</Text>
                </View>
                <View style={styles.leaveInfoModal}>
                  <Text style={styles.modalLabel}>Duration:</Text>
                  <Text style={styles.modalValue}>
                    {formatDate(selectedLeave.from_date)} - {formatDate(selectedLeave.to_date)}
                  </Text>
                </View>
                <View style={styles.leaveInfoModal}>
                  <Text style={styles.modalLabel}>Reason:</Text>
                  <Text style={styles.modalValueReason}>{selectedLeave.cause}</Text>
                </View>

                <View style={styles.remarksContainer}>
                  <Text style={styles.remarksLabel}>Remarks (Optional):</Text>
                  <TextInput
                    style={styles.remarksInput}
                    placeholder="Add any remarks or notes..."
                    placeholderTextColor={COLORS.gray}
                    multiline
                    numberOfLines={3}
                    value={remarks}
                    onChangeText={setRemarks}
                  />
                </View>

                <View style={styles.modalActions}>
                  <TouchableOpacity
                    style={[styles.modalButton, styles.approveButton]}
                    onPress={() => {
                      Alert.alert(
                        'Approve Leave',
                        'Are you sure you want to approve this leave application?',
                        [
                          { text: 'Cancel', style: 'cancel' },
                          { 
                            text: 'Approve', 
                            onPress: () => handleStatusUpdate(selectedLeave.id, 'APPROVED')
                          }
                        ]
                      );
                    }}
                  >
                    <Ionicons name="checkmark-circle" size={24} color={COLORS.white} />
                    <Text style={styles.modalButtonText}>Approve</Text>
                  </TouchableOpacity>

                  <TouchableOpacity
                    style={[styles.modalButton, styles.rejectButton]}
                    onPress={() => {
                      Alert.alert(
                        'Reject Leave',
                        'Are you sure you want to reject this leave application?',
                        [
                          { text: 'Cancel', style: 'cancel' },
                          { 
                            text: 'Reject', 
                            style: 'destructive',
                            onPress: () => handleStatusUpdate(selectedLeave.id, 'REJECTED')
                          }
                        ]
                      );
                    }}
                  >
                    <Ionicons name="close-circle" size={24} color={COLORS.white} />
                    <Text style={styles.modalButtonText}>Reject</Text>
                  </TouchableOpacity>
                </View>
              </View>
            )}
          </View>
        </View>
      </Modal>
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

function LeaveCard({ 
  leave, 
  formatDate, 
  calculateDays, 
  getStatusColor, 
  getStatusIcon,
  openUpdateModal 
}) {
  const [expanded, setExpanded] = useState(false);
  const status = leave.status || 'PENDING';
  const statusColor = getStatusColor(status);
  const days = calculateDays(leave.from_date, leave.to_date);

  return (
    <View style={styles.leaveCard}>
      <View style={[styles.statusBar, { backgroundColor: statusColor }]} />
      
      <TouchableOpacity 
        activeOpacity={0.7} 
        onPress={() => setExpanded(!expanded)}
        style={styles.leaveContent}
      >
        <View style={styles.leaveHeader}>
          <View style={[styles.studentIcon, { backgroundColor: statusColor + '20' }]}>
            <Ionicons name="person" size={24} color={statusColor} />
          </View>
          
          <View style={styles.studentInfo}>
            <Text style={styles.studentId}>Student ID: {leave.student_id}</Text>
            <View style={styles.dateRow}>
              <Ionicons name="calendar-outline" size={14} color={COLORS.gray} />
              <Text style={styles.dateText}>
                {formatDate(leave.from_date)} - {formatDate(leave.to_date)}
              </Text>
            </View>
            <View style={styles.daysRow}>
              <Ionicons name="time-outline" size={14} color={COLORS.accent} />
              <Text style={styles.daysText}>{days} {days === 1 ? 'day' : 'days'}</Text>
            </View>
          </View>

          <View style={[styles.statusBadge, { backgroundColor: statusColor + '15' }]}>
            <Ionicons name={getStatusIcon(status)} size={16} color={statusColor} />
            <Text style={[styles.statusText, { color: statusColor }]}>
              {status}
            </Text>
          </View>
        </View>

        {expanded && (
          <View style={styles.expandedContent}>
            <View style={styles.reasonSection}>
              <Text style={styles.reasonLabel}>Reason for Leave:</Text>
              <Text style={styles.reasonText}>{leave.cause}</Text>
            </View>

            {leave.remarks && (
              <View style={styles.remarksSection}>
                <Text style={styles.remarksLabelCard}>Admin Remarks:</Text>
                <Text style={styles.remarksTextCard}>{leave.remarks}</Text>
              </View>
            )}
          </View>
        )}
      </TouchableOpacity>

      <View style={styles.cardActions}>
        <TouchableOpacity 
          style={styles.expandButtonCard}
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

        {status === 'PENDING' && (
          <TouchableOpacity 
            style={styles.updateButton}
            onPress={() => openUpdateModal(leave)}
          >
            <Ionicons name="create" size={20} color={COLORS.white} />
            <Text style={styles.updateButtonText}>Update</Text>
          </TouchableOpacity>
        )}
      </View>
    </View>
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
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  refreshButton: {
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
  statsRow: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    marginBottom: 20,
    gap: 8,
  },
  statCard: {
    flex: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    borderRadius: 12,
    padding: 12,
    alignItems: 'center',
    gap: 4,
  },
  statValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  statLabel: {
    fontSize: 10,
    color: COLORS.white,
    opacity: 0.9,
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    marginHorizontal: 20,
    paddingHorizontal: 15,
    borderRadius: 15,
    height: 50,
  },
  searchInput: {
    flex: 1,
    marginLeft: 10,
    fontSize: 15,
    color: COLORS.primary,
  },
  filterContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingVertical: 15,
    gap: 8,
  },
  filterButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    paddingHorizontal: 8,
    borderRadius: 12,
    backgroundColor: COLORS.white,
    gap: 4,
  },
  filterButtonActive: {
    backgroundColor: COLORS.secondary,
  },
  filterButtonText: {
    fontSize: 12,
    fontWeight: '600',
    color: COLORS.gray,
  },
  filterButtonTextActive: {
    color: COLORS.white,
  },
  countBadge: {
    backgroundColor: COLORS.lightGray,
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 10,
  },
  countBadgeActive: {
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
  },
  countText: {
    fontSize: 10,
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
  leaveCard: {
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
  statusBar: {
    height: 4,
  },
  leaveContent: {
    padding: 15,
  },
  leaveHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
  },
  studentIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  studentInfo: {
    flex: 1,
  },
  studentId: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 4,
  },
  dateRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 4,
  },
  dateText: {
    fontSize: 13,
    color: COLORS.gray,
  },
  daysRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  daysText: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.accent,
  },
  statusBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 12,
    gap: 4,
  },
  statusText: {
    fontSize: 11,
    fontWeight: 'bold',
  },
  expandedContent: {
    marginTop: 15,
    paddingTop: 15,
    borderTopWidth: 1,
    borderTopColor: COLORS.lightGray + '50',
  },
  reasonSection: {
    backgroundColor: COLORS.background,
    padding: 12,
    borderRadius: 10,
    marginBottom: 12,
  },
  reasonLabel: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 6,
  },
  reasonText: {
    fontSize: 14,
    color: COLORS.gray,
    lineHeight: 20,
  },
  remarksSection: {
    backgroundColor: '#FFF3E0',
    padding: 12,
    borderRadius: 10,
  },
  remarksLabelCard: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#F57C00',
    marginBottom: 6,
  },
  remarksTextCard: {
    fontSize: 14,
    color: COLORS.gray,
    lineHeight: 20,
  },
  cardActions: {
    flexDirection: 'row',
    borderTopWidth: 1,
    borderTopColor: COLORS.lightGray + '50',
  },
  expandButtonCard: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    gap: 6,
    borderRightWidth: 1,
    borderRightColor: COLORS.lightGray + '50',
  },
  expandButtonText: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.secondary,
  },
  updateButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 12,
    backgroundColor: COLORS.secondary,
    gap: 6,
  },
  updateButtonText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  modalContainer: {
    flex: 1,
    justifyContent: 'flex-end',
  },
  modalBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
  },
  modalContent: {
    backgroundColor: COLORS.white,
    borderTopLeftRadius: 30,
    borderTopRightRadius: 30,
    maxHeight: '80%',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 20,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.lightGray,
  },
  modalTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  modalBody: {
    padding: 20,
  },
  leaveInfoModal: {
    marginBottom: 15,
  },
  modalLabel: {
    fontSize: 13,
    color: COLORS.gray,
    marginBottom: 4,
  },
  modalValue: {
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.primary,
  },
  modalValueReason: {
    fontSize: 15,
    color: COLORS.gray,
    lineHeight: 22,
  },
  remarksContainer: {
    marginTop: 10,
    marginBottom: 20,
  },
  remarksLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 8,
  },
  remarksInput: {
    backgroundColor: COLORS.background,
    borderRadius: 12,
    padding: 12,
    fontSize: 14,
    color: COLORS.primary,
    minHeight: 80,
    textAlignVertical: 'top',
  },
  modalActions: {
    flexDirection: 'row',
    gap: 12,
  },
  modalButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 14,
    borderRadius: 15,
    gap: 8,
  },
  approveButton: {
    backgroundColor: COLORS.success,
  },
  rejectButton: {
    backgroundColor: COLORS.error,
  },
  modalButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.white,
  },
});