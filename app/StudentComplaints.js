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
  resolved: '#4CAF50',
  rejected: '#F44336',
  inProgress: '#2196F3',
};

export default function StudentComplaintsScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [complaints, setComplaints] = useState([]);
  const [error, setError] = useState('');
  const [studentInfo, setStudentInfo] = useState(null);

  useEffect(() => {
    fetchComplaints();
  }, []);

  const fetchComplaints = async () => {
    try {
      setLoading(true);
      setError('');

      const studentId = await AsyncStorage.getItem('student_id');
      
      if (!studentId) {
        Alert.alert('Error', 'Student ID not found. Please login again.');
        router.replace('/');
        return;
      }

      console.log('Fetching complaints for student:', studentId);

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=st_complaints',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ student_id: studentId }),
        }
      );

      const result = await response.json();
      console.log('Complaints response:', result);

      if (result.status === 'success' && result.data) {
        setComplaints(result.data);
        
        // Store student info from first complaint
        if (result.data.length > 0) {
          setStudentInfo({
            name: result.data[0].student_name,
            class: result.data[0].student_class,
            section: result.data[0].student_section,
            roll: result.data[0].student_roll,
          });
        }

        // Cache complaints
        await AsyncStorage.setItem('cached_complaints', JSON.stringify(result.data));
      } else {
        setComplaints([]);
        setError('No complaints found');
      }
    } catch (err) {
      console.error('Error fetching complaints:', err);
      setError('Failed to load complaints. Please try again.');
      
      // Try to load cached data
      try {
        const cachedData = await AsyncStorage.getItem('cached_complaints');
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

  const onRefresh = () => {
    setRefreshing(true);
    fetchComplaints();
  };

  const handleAddComplaint = () => {
    router.push('/ComplaintScreen');
  };

  const handleBack = () => {
    router.back();
  };

  const getStatusColor = (status) => {
    switch (status?.toUpperCase()) {
      case 'RESOLVED':
        return COLORS.resolved;
      case 'PENDING':
        return COLORS.pending;
      case 'REJECTED':
        return COLORS.rejected;
      case 'IN_PROGRESS':
      case 'IN PROGRESS':
        return COLORS.inProgress;
      default:
        return COLORS.pending;
    }
  };

  const getStatusIcon = (status) => {
    switch (status?.toUpperCase()) {
      case 'RESOLVED':
        return 'checkmark-circle';
      case 'PENDING':
        return 'time';
      case 'REJECTED':
        return 'close-circle';
      case 'IN_PROGRESS':
      case 'IN PROGRESS':
        return 'hourglass';
      default:
        return 'time';
    }
  };

  const getStatusLabel = (status) => {
    switch (status?.toUpperCase()) {
      case 'RESOLVED':
        return 'Resolved';
      case 'PENDING':
        return 'Pending';
      case 'REJECTED':
        return 'Rejected';
      case 'IN_PROGRESS':
      case 'IN PROGRESS':
        return 'In Progress';
      default:
        return status || 'Pending';
    }
  };

  const stats = {
    total: complaints.length,
    pending: complaints.filter(c => c.status?.toUpperCase() === 'PENDING').length,
    resolved: complaints.filter(c => c.status?.toUpperCase() === 'RESOLVED').length,
    rejected: complaints.filter(c => c.status?.toUpperCase() === 'REJECTED').length,
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <LinearGradient
          colors={[COLORS.primary, COLORS.secondary]}
          style={styles.loadingGradient}
        >
          <View style={styles.logoCircle}>
            <Ionicons name="chatbubbles" size={40} color={COLORS.primary} />
          </View>
          <Text style={styles.loadingText}>Loading Complaints...</Text>
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

          <Text style={styles.headerTitle}>My Complaints</Text>

          {/* Add Complaint Button */}
          <TouchableOpacity 
            style={styles.addButton} 
            onPress={handleAddComplaint}
            activeOpacity={0.7}
          >
            <Ionicons name="add" size={28} color={COLORS.white} />
          </TouchableOpacity>
        </View>

        {/* Student Info */}
        {studentInfo && (
          <View style={styles.studentInfoContainer}>
            <View style={styles.studentAvatar}>
              <Ionicons name="person" size={24} color={COLORS.primary} />
            </View>
            <View style={styles.studentDetails}>
              <Text style={styles.studentName}>{studentInfo.name}</Text>
              <Text style={styles.studentClass}>
                Class {studentInfo.class} - {studentInfo.section} | Roll: {studentInfo.roll}
              </Text>
            </View>
          </View>
        )}

        {/* Stats */}
        <View style={styles.statsRow}>
          <View style={styles.statCard}>
            <Text style={styles.statValue}>{stats.total}</Text>
            <Text style={styles.statLabel}>Total</Text>
          </View>
          <View style={styles.statCard}>
            <View style={[styles.statDot, { backgroundColor: COLORS.pending }]} />
            <Text style={styles.statValue}>{stats.pending}</Text>
            <Text style={styles.statLabel}>Pending</Text>
          </View>
          <View style={styles.statCard}>
            <View style={[styles.statDot, { backgroundColor: COLORS.resolved }]} />
            <Text style={styles.statValue}>{stats.resolved}</Text>
            <Text style={styles.statLabel}>Resolved</Text>
          </View>
          <View style={styles.statCard}>
            <View style={[styles.statDot, { backgroundColor: COLORS.rejected }]} />
            <Text style={styles.statValue}>{stats.rejected}</Text>
            <Text style={styles.statLabel}>Rejected</Text>
          </View>
        </View>
      </LinearGradient>

      {/* Error Banner */}
      {error ? (
        <View style={styles.errorBanner}>
          <Ionicons name="information-circle" size={18} color="#F57C00" />
          <Text style={styles.errorBannerText}>{error}</Text>
          <TouchableOpacity onPress={fetchComplaints}>
            <Ionicons name="refresh" size={18} color="#F57C00" />
          </TouchableOpacity>
        </View>
      ) : null}

      {/* Complaints List */}
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
        {complaints.length === 0 ? (
          <View style={styles.emptyContainer}>
            <View style={styles.emptyIconContainer}>
              <Ionicons name="chatbubbles-outline" size={80} color={COLORS.lightGray} />
            </View>
            <Text style={styles.emptyTitle}>No Complaints Yet</Text>
            <Text style={styles.emptyText}>
              You haven't submitted any complaints yet.{'\n'}
              Tap the + button to file a new complaint.
            </Text>
            <TouchableOpacity 
              style={styles.emptyButton}
              onPress={handleAddComplaint}
            >
              <Ionicons name="add-circle" size={20} color={COLORS.white} />
              <Text style={styles.emptyButtonText}>File New Complaint</Text>
            </TouchableOpacity>
          </View>
        ) : (
          <>
            <Text style={styles.sectionTitle}>
              Your Complaints ({complaints.length})
            </Text>
            
            {complaints.map((complaint, index) => (
              <ComplaintCard
                key={complaint.id || index}
                complaint={complaint}
                getStatusColor={getStatusColor}
                getStatusIcon={getStatusIcon}
                getStatusLabel={getStatusLabel}
              />
            ))}
          </>
        )}

        <View style={{ height: 30 }} />
      </ScrollView>

      {/* Floating Action Button */}
      {complaints.length > 0 && (
        <TouchableOpacity 
          style={styles.fab}
          onPress={handleAddComplaint}
          activeOpacity={0.8}
        >
          <LinearGradient
            colors={[COLORS.primary, COLORS.secondary]}
            style={styles.fabGradient}
          >
            <Ionicons name="add" size={30} color={COLORS.white} />
          </LinearGradient>
        </TouchableOpacity>
      )}
    </View>
  );
}

// Complaint Card Component
function ComplaintCard({ complaint, getStatusColor, getStatusIcon, getStatusLabel }) {
  const [expanded, setExpanded] = useState(false);
  const statusColor = getStatusColor(complaint.status);

  return (
    <TouchableOpacity
      style={styles.complaintCard}
      onPress={() => setExpanded(!expanded)}
      activeOpacity={0.7}
    >
      {/* Status Bar */}
      <View style={[styles.statusBar, { backgroundColor: statusColor }]} />

      <View style={styles.cardContent}>
        {/* Header Row */}
        <View style={styles.cardHeader}>
          <View style={styles.cardHeaderLeft}>
            <View style={[styles.complaintIcon, { backgroundColor: statusColor + '20' }]}>
              <Ionicons name="chatbubble-ellipses" size={22} color={statusColor} />
            </View>
            <View style={styles.complaintInfo}>
              <Text style={styles.complaintTo}>To: {complaint.complaint_to}</Text>
              <Text style={styles.complaintId}>Complaint #{complaint.id}</Text>
            </View>
          </View>

          {/* Status Badge */}
          <View style={[styles.statusBadge, { backgroundColor: statusColor + '15' }]}>
            <Ionicons 
              name={getStatusIcon(complaint.status)} 
              size={14} 
              color={statusColor} 
            />
            <Text style={[styles.statusText, { color: statusColor }]}>
              {getStatusLabel(complaint.status)}
            </Text>
          </View>
        </View>

        {/* Complaint Text */}
        <View style={styles.complaintTextContainer}>
          <Text 
            style={styles.complaintText}
            numberOfLines={expanded ? undefined : 2}
          >
            {complaint.complaint}
          </Text>
        </View>

        {/* Expand/Collapse Indicator */}
        {complaint.complaint && complaint.complaint.length > 100 && (
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
              <Ionicons name="person-outline" size={16} color={COLORS.gray} />
              <Text style={styles.detailText}>{complaint.student_name}</Text>
            </View>
            <View style={styles.detailRow}>
              <Ionicons name="school-outline" size={16} color={COLORS.gray} />
              <Text style={styles.detailText}>
                Class {complaint.student_class} - {complaint.student_section}
              </Text>
            </View>
            <View style={styles.detailRow}>
              <Ionicons name="call-outline" size={16} color={COLORS.gray} />
              <Text style={styles.detailText}>{complaint.student_mobile}</Text>
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
    fontSize: 20,
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
  studentInfoContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    marginBottom: 20,
  },
  studentAvatar: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: COLORS.white,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  studentDetails: {
    flex: 1,
  },
  studentName: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: 4,
  },
  studentClass: {
    fontSize: 13,
    color: COLORS.white,
    opacity: 0.9,
  },
  statsRow: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    gap: 10,
  },
  statCard: {
    flex: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    borderRadius: 12,
    padding: 12,
    alignItems: 'center',
  },
  statDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
    marginBottom: 4,
  },
  statValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  statLabel: {
    fontSize: 11,
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
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 15,
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
  complaintCard: {
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
  cardHeaderLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  complaintIcon: {
    width: 44,
    height: 44,
    borderRadius: 22,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  complaintInfo: {
    flex: 1,
  },
  complaintTo: {
    fontSize: 15,
    fontWeight: '700',
    color: COLORS.primary,
    marginBottom: 2,
  },
  complaintId: {
    fontSize: 12,
    color: COLORS.gray,
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
    fontSize: 11,
    fontWeight: '700',
  },
  complaintTextContainer: {
    backgroundColor: COLORS.background,
    padding: 12,
    borderRadius: 10,
    marginBottom: 8,
  },
  complaintText: {
    fontSize: 14,
    color: COLORS.gray,
    lineHeight: 22,
  },
  expandRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
    paddingTop: 8,
  },
  expandText: {
    fontSize: 13,
    color: COLORS.secondary,
    fontWeight: '600',
  },
  expandedDetails: {
    marginTop: 12,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: COLORS.lightGray,
    gap: 10,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  detailText: {
    fontSize: 13,
    color: COLORS.gray,
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