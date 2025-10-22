import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
    Alert,
    Linking,
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
  active: '#FFC107',
  resolved: '#4CAF50',
  closed: '#9E9E9E',
};

const COMPLAINT_COLORS = [
  '#FF6B6B',
  '#4ECDC4',
  '#45B7D1',
  '#FFA07A',
  '#98D8C8',
  '#F7DC6F',
  '#BB8FCE',
  '#85C1E2',
];

export default function AdminComplaintListScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [complaints, setComplaints] = useState([]);
  const [filteredComplaints, setFilteredComplaints] = useState([]);
  const [error, setError] = useState('');
  const [selectedFilter, setSelectedFilter] = useState('all');
  const [searchQuery, setSearchQuery] = useState('');
  const [modalVisible, setModalVisible] = useState(false);
  const [selectedComplaint, setSelectedComplaint] = useState(null);
  const [response, setResponse] = useState('');

  useEffect(() => {
    checkAdminAccess();
  }, []);

  useEffect(() => {
    filterComplaints();
  }, [selectedFilter, searchQuery, complaints]);

  const checkAdminAccess = async () => {
    const userType = await AsyncStorage.getItem('user_type');
    if (userType !== 'ADMIN') {
      Alert.alert('Access Denied', 'This section is only for administrators.');
      router.back();
      return;
    }
    fetchComplaints();
  };

  const fetchComplaints = async () => {
    try {
      setLoading(true);
      setError('');

      const userType = await AsyncStorage.getItem('user_type');

      const apiResponse = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=complaints',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ user_type: userType }),
        }
      );

      const result = await apiResponse.json();
      
      if (result.status === 'success' && result.data && Array.isArray(result.data)) {
        setComplaints(result.data);
        
        // Cache data
        await AsyncStorage.setItem('cached_admin_complaints', JSON.stringify(result.data));
      } else if (result.status === 'error') {
        setError(result.msg || 'You are not authorized');
        Alert.alert('Error', result.msg || 'You are not authorized');
        router.back();
      } else {
        setError('No complaints found');
        setComplaints([]);
      }
    } catch (err) {
      console.error('Error fetching complaints:', err);
      setError('Failed to load complaints');
      
      // Try to load cached data
      try {
        const cachedData = await AsyncStorage.getItem('cached_admin_complaints');
        if (cachedData) {
          setComplaints(JSON.parse(cachedData));
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

  const filterComplaints = () => {
    let filtered = complaints;

    // Apply status filter
    if (selectedFilter !== 'all') {
      filtered = filtered.filter(complaint => 
        complaint.status.toLowerCase() === selectedFilter.toLowerCase()
      );
    }

    // Apply search filter
    if (searchQuery.trim()) {
      filtered = filtered.filter(complaint =>
        complaint.student_name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        complaint.complaint.toLowerCase().includes(searchQuery.toLowerCase()) ||
        complaint.complaint_to.toLowerCase().includes(searchQuery.toLowerCase()) ||
        complaint.student_class?.toLowerCase().includes(searchQuery.toLowerCase()) ||
        complaint.student_roll?.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }

    setFilteredComplaints(filtered);
  };

  const onRefresh = () => {
    setRefreshing(true);
    fetchComplaints();
  };

 const handleStatusUpdate = async (complaintId, newStatus) => {
  // Close modal and optimistically update
  setModalVisible(false);
  
  const updatedComplaints = complaints.map(complaint => 
    complaint.id === complaintId 
      ? { ...complaint, status: newStatus, response: response || '' }
      : complaint
  );
  setComplaints(updatedComplaints);

  try {
    // Get admin ID from AsyncStorage
    const adminId = await AsyncStorage.getItem('user_id');
    
    const apiResponse = await fetch(
      'https://dpsmushkipur.com/bine/api.php?task=update_complaints',
      {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          id: complaintId,
          status: newStatus,
          response: response || '',
          updated_by: adminId || '0',
        }),
      }
    );

    const result = await apiResponse.json();
    
    if (result.status === 'success') {
      Alert.alert('Success', `Complaint ${newStatus.toLowerCase()} successfully!`);
      setResponse('');
      setSelectedComplaint(null);
      
      // Refresh data
      setTimeout(() => {
        fetchComplaints();
      }, 1000);
    } else {
      Alert.alert('Error', result.msg || 'Failed to update complaint status');
      fetchComplaints(); // Reload to get correct state
    }
  } catch (err) {
    console.error('Error updating complaint:', err);
    Alert.alert('Error', 'Network error occurred');
    fetchComplaints();
  }
};

  const openUpdateModal = (complaint) => {
    setSelectedComplaint(complaint);
    setResponse('');
    setModalVisible(true);
  };

  const handleCall = (mobile) => {
    if (mobile) {
      Linking.openURL(`tel:${mobile}`).catch(err => {
        Alert.alert('Error', 'Unable to make a call');
      });
    }
  };

  const getStatusColor = (status) => {
    switch (status?.toUpperCase()) {
      case 'ACTIVE': return COLORS.active;
      case 'RESOLVED': return COLORS.resolved;
      case 'CLOSED': return COLORS.closed;
      default: return COLORS.active;
    }
  };

  const getStatusIcon = (status) => {
    switch (status?.toUpperCase()) {
      case 'ACTIVE': return 'alert-circle';
      case 'RESOLVED': return 'checkmark-circle';
      case 'CLOSED': return 'close-circle';
      default: return 'alert-circle';
    }
  };

  const getComplaintColor = (index) => {
    return COMPLAINT_COLORS[index % COMPLAINT_COLORS.length];
  };

  const stats = {
    total: complaints.length,
    active: complaints.filter(c => c.status === 'ACTIVE').length,
    resolved: complaints.filter(c => c.status === 'RESOLVED').length,
    closed: complaints.filter(c => c.status === 'CLOSED').length,
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <View style={styles.logoCircle}>
          <Ionicons name="chatbubbles" size={40} color={COLORS.primary} />
        </View>
        <Text style={styles.loadingText}>Loading complaints...</Text>
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
          <Text style={styles.headerTitle}>Complaints</Text>
          <TouchableOpacity style={styles.refreshButton} onPress={onRefresh}>
            <Ionicons name="refresh" size={24} color={COLORS.white} />
          </TouchableOpacity>
        </View>

        {/* Stats */}
        <View style={styles.statsRow}>
          <View style={styles.statCard}>
            <Ionicons name="chatbubbles" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.total}</Text>
            <Text style={styles.statLabel}>Total</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="alert-circle" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.active}</Text>
            <Text style={styles.statLabel}>Active</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="checkmark-circle" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.resolved}</Text>
            <Text style={styles.statLabel}>Resolved</Text>
          </View>
          <View style={styles.statCard}>
            <Ionicons name="close-circle" size={20} color={COLORS.white} />
            <Text style={styles.statValue}>{stats.closed}</Text>
            <Text style={styles.statLabel}>Closed</Text>
          </View>
        </View>

        {/* Search Bar */}
        <View style={styles.searchContainer}>
          <Ionicons name="search" size={20} color={COLORS.gray} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search by name, class or complaint..."
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
          count={complaints.length}
        />
        <FilterButton
          title="Active"
          active={selectedFilter === 'active'}
          onPress={() => setSelectedFilter('active')}
          count={stats.active}
        />
        <FilterButton
          title="Resolved"
          active={selectedFilter === 'resolved'}
          onPress={() => setSelectedFilter('resolved')}
          count={stats.resolved}
        />
        <FilterButton
          title="Closed"
          active={selectedFilter === 'closed'}
          onPress={() => setSelectedFilter('closed')}
          count={stats.closed}
        />
      </View>

      {/* Error Banner */}
      {error && (
        <View style={styles.errorBanner}>
          <Ionicons name="information-circle" size={16} color="#F57C00" />
          <Text style={styles.errorBannerText}>{error}</Text>
        </View>
      )}

      {/* Complaints List */}
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
        {filteredComplaints.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="chatbubbles-outline" size={80} color={COLORS.lightGray} />
            <Text style={styles.emptyText}>
              {searchQuery ? 'No matching complaints' : 'No complaints found'}
            </Text>
          </View>
        ) : (
          filteredComplaints.map((complaint, index) => (
            <ComplaintCard
              key={complaint.id}
              complaint={complaint}
              index={index}
              getStatusColor={getStatusColor}
              getStatusIcon={getStatusIcon}
              getComplaintColor={getComplaintColor}
              openUpdateModal={openUpdateModal}
              handleCall={handleCall}
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
              <Text style={styles.modalTitle}>Update Complaint</Text>
              <TouchableOpacity onPress={() => setModalVisible(false)}>
                <Ionicons name="close" size={28} color={COLORS.gray} />
              </TouchableOpacity>
            </View>

            {selectedComplaint && (
              <ScrollView style={styles.modalBody} showsVerticalScrollIndicator={false}>
                <View style={styles.studentCard}>
                  <View style={styles.studentCardHeader}>
                    <Ionicons name="person-circle" size={24} color={COLORS.primary} />
                    <Text style={styles.studentCardTitle}>Student Information</Text>
                  </View>
                  <View style={styles.studentCardRow}>
                    <Text style={styles.modalLabel}>Name:</Text>
                    <Text style={styles.modalValue}>{selectedComplaint.student_name}</Text>
                  </View>
                  <View style={styles.studentCardRow}>
                    <Text style={styles.modalLabel}>Class:</Text>
                    <Text style={styles.modalValue}>
                      {selectedComplaint.student_class} - {selectedComplaint.student_section}
                    </Text>
                  </View>
                  <View style={styles.studentCardRow}>
                    <Text style={styles.modalLabel}>Roll No:</Text>
                    <Text style={styles.modalValue}>{selectedComplaint.student_roll}</Text>
                  </View>
                  <View style={styles.studentCardRow}>
                    <Text style={styles.modalLabel}>Mobile:</Text>
                    <TouchableOpacity onPress={() => handleCall(selectedComplaint.student_mobile)}>
                      <Text style={styles.mobileValue}>
                        <Ionicons name="call" size={14} color={COLORS.secondary} /> {selectedComplaint.student_mobile}
                      </Text>
                    </TouchableOpacity>
                  </View>
                </View>

                <View style={styles.complaintInfoModal}>
                  <Text style={styles.modalLabel}>Complaint To:</Text>
                  <Text style={styles.modalValue}>{selectedComplaint.complaint_to}</Text>
                </View>
                
                <View style={styles.complaintInfoModal}>
                  <Text style={styles.modalLabel}>Complaint:</Text>
                  <Text style={styles.modalValueComplaint}>{selectedComplaint.complaint}</Text>
                </View>

                <View style={styles.responseContainer}>
                  <Text style={styles.responseLabel}>Admin Response:</Text>
                  <TextInput
                    style={styles.responseInput}
                    placeholder="Add your response or notes..."
                    placeholderTextColor={COLORS.gray}
                    multiline
                    numberOfLines={4}
                    value={response}
                    onChangeText={setResponse}
                  />
                </View>

                <View style={styles.modalActions}>
                  <TouchableOpacity
                    style={[styles.modalButton, styles.resolveButton]}
                    onPress={() => {
                      Alert.alert(
                        'Resolve Complaint',
                        'Mark this complaint as resolved?',
                        [
                          { text: 'Cancel', style: 'cancel' },
                          { 
                            text: 'Resolve', 
                            onPress: () => handleStatusUpdate(selectedComplaint.id, 'RESOLVED')
                          }
                        ]
                      );
                    }}
                  >
                    <Ionicons name="checkmark-circle" size={24} color={COLORS.white} />
                    <Text style={styles.modalButtonText}>Resolve</Text>
                  </TouchableOpacity>

                  <TouchableOpacity
                    style={[styles.modalButton, styles.closeButton]}
                    onPress={() => {
                      Alert.alert(
                        'Close Complaint',
                        'Close this complaint?',
                        [
                          { text: 'Cancel', style: 'cancel' },
                          { 
                            text: 'Close', 
                            style: 'destructive',
                            onPress: () => handleStatusUpdate(selectedComplaint.id, 'CLOSED')
                          }
                        ]
                      );
                    }}
                  >
                    <Ionicons name="close-circle" size={24} color={COLORS.white} />
                    <Text style={styles.modalButtonText}>Close</Text>
                  </TouchableOpacity>
                </View>
              </ScrollView>
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

function ComplaintCard({ 
  complaint, 
  index,
  getStatusColor, 
  getStatusIcon,
  getComplaintColor,
  openUpdateModal,
  handleCall 
}) {
  const [expanded, setExpanded] = useState(false);
  const statusColor = getStatusColor(complaint.status);
  const complaintColor = getComplaintColor(index);

  return (
    <View style={styles.complaintCard}>
      <View style={[styles.statusBar, { backgroundColor: statusColor }]} />
      
      <TouchableOpacity 
        activeOpacity={0.7} 
        onPress={() => setExpanded(!expanded)}
        style={styles.complaintContent}
      >
        <View style={styles.complaintHeader}>
          <View style={[styles.studentIcon, { backgroundColor: complaintColor + '20' }]}>
            <Ionicons name="person" size={24} color={complaintColor} />
          </View>
          
          <View style={styles.studentInfo}>
            <Text style={styles.studentName}>{complaint.student_name}</Text>
            <View style={styles.infoRow}>
              <Ionicons name="school-outline" size={14} color={COLORS.gray} />
              <Text style={styles.classText}>
                Class {complaint.student_class}-{complaint.student_section} • Roll {complaint.student_roll}
              </Text>
            </View>
            <View style={styles.infoRow}>
              <Ionicons name="arrow-forward" size={14} color={COLORS.gray} />
              <Text style={styles.complaintTo}>To: {complaint.complaint_to}</Text>
            </View>
          </View>

          <View style={[styles.statusBadge, { backgroundColor: statusColor + '15' }]}>
            <Ionicons name={getStatusIcon(complaint.status)} size={16} color={statusColor} />
            <Text style={[styles.statusText, { color: statusColor }]}>
              {complaint.status}
            </Text>
          </View>
        </View>

        <View style={styles.complaintPreview}>
          <Text style={styles.complaintText} numberOfLines={expanded ? undefined : 2}>
            {complaint.complaint}
          </Text>
        </View>

        {expanded && (
          <View style={styles.expandedContent}>
            <TouchableOpacity 
              style={styles.contactButton}
              onPress={() => handleCall(complaint.student_mobile)}
            >
              <Ionicons name="call" size={18} color={COLORS.secondary} />
              <Text style={styles.contactText}>{complaint.student_mobile}</Text>
            </TouchableOpacity>

            {complaint.response && (
              <View style={styles.responseSection}>
                <Text style={styles.responseLabel}>Admin Response:</Text>
                <Text style={styles.responseText}>{complaint.response}</Text>
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

        {complaint.status === 'ACTIVE' && (
          <TouchableOpacity 
            style={styles.updateButton}
            onPress={() => openUpdateModal(complaint)}
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
    padding: 10,
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
    paddingHorizontal: 6,
    borderRadius: 12,
    backgroundColor: COLORS.white,
    gap: 4,
  },
  filterButtonActive: {
    backgroundColor: COLORS.secondary,
  },
  filterButtonText: {
    fontSize: 11,
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
  complaintCard: {
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
  complaintContent: {
    padding: 15,
  },
  complaintHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 12,
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
  studentName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 4,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 3,
  },
  classText: {
    fontSize: 12,
    color: COLORS.gray,
  },
  complaintTo: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.secondary,
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
  complaintPreview: {
    backgroundColor: COLORS.background,
    padding: 12,
    borderRadius: 10,
  },
  complaintText: {
    fontSize: 14,
    color: COLORS.gray,
    lineHeight: 20,
  },
  expandedContent: {
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: COLORS.lightGray + '50',
  },
  contactButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.secondary + '15',
    paddingHorizontal: 15,
    paddingVertical: 10,
    borderRadius: 10,
    gap: 8,
    alignSelf: 'flex-start',
    marginBottom: 12,
  },
  contactText: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.secondary,
  },
  responseSection: {
    backgroundColor: '#E8F5E9',
    padding: 12,
    borderRadius: 10,
  },
  responseLabel: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.secondary,
    marginBottom: 6,
  },
  responseText: {
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
    maxHeight: '85%',
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
  studentCard: {
    backgroundColor: COLORS.background,
    borderRadius: 15,
    padding: 15,
    marginBottom: 15,
  },
  studentCardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
    gap: 8,
  },
  studentCardTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  studentCardRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  complaintInfoModal: {
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
  mobileValue: {
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.secondary,
  },
  modalValueComplaint: {
    fontSize: 15,
    color: COLORS.gray,
    lineHeight: 22,
  },
  responseContainer: {
    marginTop: 10,
    marginBottom: 20,
  },
  responseLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 8,
  },
  responseInput: {
    backgroundColor: COLORS.background,
    borderRadius: 12,
    padding: 12,
    fontSize: 14,
    color: COLORS.primary,
    minHeight: 100,
    textAlignVertical: 'top',
  },
  modalActions: {
    flexDirection: 'row',
    gap: 12,
    marginBottom: 20,
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
  resolveButton: {
    backgroundColor: COLORS.success,
  },
  closeButton: {
    backgroundColor: COLORS.closed,
  },
  modalButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.white,
  },
});