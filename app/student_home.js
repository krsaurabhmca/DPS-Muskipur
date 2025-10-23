import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import * as FileSystem from 'expo-file-system';
import { LinearGradient } from 'expo-linear-gradient';
import { useLocalSearchParams, useRouter } from 'expo-router';
import * as Sharing from 'expo-sharing';
import { useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Animated,
  Dimensions,
  Image,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
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
};

const FEATURES = [
  {
    id: 'attendance',
    title: 'Attendance',
    icon: 'calendar-outline',
    route: '/AttendanceScreen',
    color: '#4CAF50',
    gradient: ['#4CAF50', '#81C784'],
  },
  {
    id: 'homework',
    title: 'Homework',
    icon: 'book-outline',
    route: '/HomeWorkScreen',
    color: '#2196F3',
    gradient: ['#2196F3', '#64B5F6'],
  },
  {
    id: 'exam-report',
    title: 'Exam Report',
    icon: 'trophy-outline',
    route: '/ExamReportScreen',
    color: '#FF9800',
    gradient: ['#FF9800', '#FFB74D'],
  },
  {
    id: 'noticeboard',
    title: 'Notice Board',
    icon: 'notifications-outline',
    route: '/noticeboard',
    color: '#E91E63',
    gradient: ['#E91E63', '#F06292'],
  },
  {
    id: 'holiday',
    title: 'Holidays',
    icon: 'sunny-outline',
    route: '/HolidayScreen',
    color: '#9C27B0',
    gradient: ['#9C27B0', '#BA68C8'],
  },
  {
    id: 'leave',
    title: 'Leave Application',
    icon: 'document-text-outline',
    route: '/StudentLeaveApply',
    color: '#00BCD4',
    gradient: ['#00BCD4', '#4DD0E1'],
  },
  {
    id: 'payment',
    title: 'Online Payment',
    icon: 'card-outline',
    route: '/OnlinePaymentScreen',
    color: '#8BC34A',
    gradient: ['#8BC34A', '#AED581'],
  },
  {
    id: 'payment-history',
    title: 'Payment History',
    icon: 'receipt-outline',
    route: '/PaymentHistoryScreen',
    color: '#FF5722',
    gradient: ['#FF5722', '#FF8A65'],
  },
  {
    id: 'bus',
    title: 'Live Bus Location',
    icon: 'bus-outline',
    route: '/LiveBusLocation',
    color: '#795548',
    gradient: ['#795548', '#A1887F'],
  },
  {
    id: 'complain',
    title: 'Complain',
    icon: 'alert-circle-outline',
    route: '/ComplaintScreen',
    color: '#F44336',
    gradient: ['#F44336', '#E57373'],
  },
  {
    id: 'help',
    title: 'Help & Support',
    icon: 'help-circle-outline',
    route: '/HelpAndSupport',
    color: '#607D8B',
    gradient: ['#607D8B', '#90A4AE'],
  },
  {
    id: 'rate',
    title: 'Rate & Review',
    icon: 'star-outline',
    route: '/RatingScreen',
    color: '#FFC107',
    gradient: ['#FFC107', '#FFD54F'],
  },
];

export default function StudentHomeScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const [student, setStudent] = useState(null);
  const [notices, setNotices] = useState([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState('');
  const [downloadingNoticeId, setDownloadingNoticeId] = useState(null);
  const [hasMultipleStudents, setHasMultipleStudents] = useState(false);
  const [allStudents, setAllStudents] = useState([]);
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(30)).current;
  const hasInitialized = useRef(false);

  useEffect(() => {
    if (!hasInitialized.current) {
      hasInitialized.current = true;
      initializeScreen();
    }
  }, []);

  const initializeScreen = async () => {
    console.log('Initializing screen...');
    console.log('Params received:', params);
    
    // Load student data first
    if (params.studentData) {
      try {
        const studentData = JSON.parse(params.studentData);
        console.log('Student data loaded from params');
        setStudent(studentData);
        await AsyncStorage.setItem('student_data', JSON.stringify(studentData));
      } catch (e) {
        console.error('Error parsing student data:', e);
      }
    }

    // Load notices
    if (params.notices) {
      try {
        const noticesData = typeof params.notices === 'string' 
          ? JSON.parse(params.notices) 
          : params.notices;
        
        console.log('Notices loaded from params:', noticesData.length);
        
        if (noticesData && Array.isArray(noticesData)) {
          setNotices(noticesData);
          await AsyncStorage.setItem('notices', JSON.stringify(noticesData));
        }
      } catch (e) {
        console.error('Error parsing notices:', e);
      }
    } else {
      // Try to load from AsyncStorage
      try {
        const cachedNotices = await AsyncStorage.getItem('notices');
        if (cachedNotices) {
          const parsedNotices = JSON.parse(cachedNotices);
          console.log('Notices loaded from cache:', parsedNotices.length);
          setNotices(parsedNotices);
        }
      } catch (e) {
        console.error('Error loading cached notices:', e);
      }
    }

    // Check if there are multiple students
    try {
      const studentsData = await AsyncStorage.getItem('all_students');
      if (studentsData) {
        const students = JSON.parse(studentsData);
        if (students && students.length > 1) {
          setHasMultipleStudents(true);
          setAllStudents(students);
        }
      }
    } catch (e) {
      console.error('Error checking multiple students:', e);
    }

    await fetchStudentProfile();
  };

  const fetchStudentProfile = async () => {
    try {
      setLoading(true);
      setError('');

      const studentId = await AsyncStorage.getItem('student_id');
      
      if (!studentId) {
        router.replace('/');
        return;
      }

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=get_student_profile',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            student_id: studentId,
          }),
        }
      );

      const data = await response.json();

      if (data && data.length > 0) {
        const studentData = data[0];
        setStudent(studentData);
        
        await AsyncStorage.setItem('student_data', JSON.stringify(studentData));

        // Start animations
        Animated.parallel([
          Animated.timing(fadeAnim, {
            toValue: 1,
            duration: 800,
            useNativeDriver: true,
          }),
          Animated.timing(slideAnim, {
            toValue: 0,
            duration: 600,
            useNativeDriver: true,
          }),
        ]).start();
      } else {
        setError('Unable to load profile. Please try again.');
      }
    } catch (err) {
      console.error('Error fetching student profile:', err);
      setError('Network error. Please check your connection.');
      
      const cachedData = await AsyncStorage.getItem('student_data');
      if (cachedData) {
        setStudent(JSON.parse(cachedData));
      }
    } finally {
      setLoading(false);
    }
  };

  const onRefresh = async () => {
    setRefreshing(true);
    await fetchStudentProfile();
    setRefreshing(false);
  };

  const handleSwitchStudent = () => {
    Alert.alert(
      'Switch Student',
      'Do you want to switch to another student profile?',
      [
        {
          text: 'Cancel',
          style: 'cancel',
        },
        {
          text: 'Switch',
          onPress: () => {
            router.push({
              pathname: '/student-selection',
              params: {
                students: JSON.stringify(allStudents),
                notices: JSON.stringify(notices),
              },
            });
          },
        },
      ],
    );
  };

  const handleViewAllNotices = () => {
    router.push({
      pathname: '/noticeboard',
      params: {
        notices: JSON.stringify(notices),
      },
    });
  };

  const handleDownloadAttachment = async (noticeId, attachment, title) => {
    if (!attachment) return;

    setDownloadingNoticeId(noticeId);

    try {
      const url = `https://dpsmushkipur.com/bine/required/upload/${attachment}`;
      const fileExtension = attachment.split('.').pop();
      const fileName = `${title.replace(/[^a-zA-Z0-9]/g, '_')}.${fileExtension}`;
      const fileUri = FileSystem.documentDirectory + fileName;

      console.log('Downloading from:', url);

      const downloadResumable = FileSystem.createDownloadResumable(
        url,
        fileUri,
        {},
        (downloadProgress) => {
          const progress = downloadProgress.totalBytesWritten / downloadProgress.totalBytesExpectedToWrite;
          console.log(`Download progress: ${Math.round(progress * 100)}%`);
        }
      );

      const result = await downloadResumable.downloadAsync();
      
      if (result && result.uri) {
        const fileInfo = await FileSystem.getInfoAsync(result.uri);
        
        if (fileInfo.exists && fileInfo.size > 0) {
          const isAvailable = await Sharing.isAvailableAsync();
          
          if (isAvailable) {
            await Sharing.shareAsync(result.uri, {
              dialogTitle: 'Open Notice Attachment',
            });
          } else {
            Alert.alert(
              'Success',
              'Attachment downloaded successfully!',
              [{ text: 'OK' }]
            );
          }
        } else {
          throw new Error('Downloaded file is empty or corrupted');
        }
      }
    } catch (error) {
      console.error('Download error:', error);
      Alert.alert(
        'Download Failed', 
        'Failed to download attachment. Please try again.'
      );
    } finally {
      setDownloadingNoticeId(null);
    }
  };

  const stripHtmlTags = (html) => {
    if (!html) return '';
    return html.replace(/<br\s*\/?>/gi, '\n').replace(/<[^>]*>/g, '');
  };

  const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const options = { day: '2-digit', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
  };

  const handleLogout = () => {
    Alert.alert(
      'Logout',
      'Are you sure you want to logout?',
      [
        {
          text: 'Cancel',
          style: 'cancel',
        },
        {
          text: 'Logout',
          onPress: async () => {
            await AsyncStorage.clear();
            router.replace('/');
          },
          style: 'destructive',
        },
      ],
    );
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <ActivityIndicator size="large" color={COLORS.secondary} />
        <Text style={styles.loadingText}>Loading profile...</Text>
      </View>
    );
  }

  if (error && !student) {
    return (
      <View style={styles.errorContainer}>
        <Ionicons name="alert-circle-outline" size={60} color={COLORS.error} />
        <Text style={styles.errorText}>{error}</Text>
        <TouchableOpacity style={styles.retryButton} onPress={fetchStudentProfile}>
          <Text style={styles.retryButtonText}>Retry</Text>
        </TouchableOpacity>
      </View>
    );
  }

  if (!student) {
    return null;
  }

  return (
    <View style={styles.container}>
      {/* Header with Gradient */}
      <LinearGradient
        colors={[COLORS.primary, COLORS.secondary]}
        style={styles.header}
      >
        <View style={styles.headerContent}>
          <View style={styles.headerLeft}>
            <Text style={styles.welcomeText}>Welcome Back!</Text>
            <Text style={styles.studentName}>{student.student_name}</Text>
            <View style={styles.classInfo}>
              <Ionicons name="school" size={14} color={COLORS.accent} />
              <Text style={styles.classText}>
                Class {student.student_class} - {student.student_section}
              </Text>
            </View>
          </View>
          <View style={styles.headerRight}>
            <TouchableOpacity
              style={styles.profileButton}
              onPress={() => router.push({
                pathname: '/student_profile',
                params: { studentData: JSON.stringify(student) }
              })}
            >
              {student.student_photo !== 'no_image.jpg' ? (
                <Image
                  source={{
                    uri: `https://dpsmushkipur.com/bine/upload/${student.student_photo}`,
                  }}
                  style={styles.profileImage}
                />
              ) : (
                <View
                  style={[
                    styles.profilePlaceholder,
                    {
                      backgroundColor:
                        student.student_sex === 'MALE' ? '#1B5E20' : '#4CAF50',
                    },
                  ]}
                >
                  <Ionicons
                    name={student.student_sex === 'MALE' ? 'person-circle' : 'female'}
                    size={50}
                    color={COLORS.white}
                  />
                </View>
              )}
            </TouchableOpacity>
            
            {/* Action Buttons Row */}
            <View style={styles.actionButtons}>
              {hasMultipleStudents && (
                <TouchableOpacity
                  style={styles.switchButton}
                  onPress={handleSwitchStudent}
                >
                  <Ionicons name="swap-horizontal" size={16} color={COLORS.accent} />
                  <Text style={styles.switchButtonText}>Switch</Text>
                </TouchableOpacity>
              )}
              <TouchableOpacity
                style={styles.logoutButton}
                onPress={handleLogout}
              >
                <Ionicons name="log-out-outline" size={16} color={COLORS.white} />
                <Text style={styles.logoutButtonText}>Logout</Text>
              </TouchableOpacity>
            </View>
          </View>
        </View>

        {/* Quick Stats */}
        <Animated.View
          style={[
            styles.statsContainer,
            { opacity: fadeAnim, transform: [{ translateY: slideAnim }] },
          ]}
        >
          <StatCard icon="cash-outline" value={`₹${student.total_paid}`} label="Total Paid" />
          <StatCard icon="alert-circle-outline" value={`₹${student.current_dues}`} label="Current Dues" />
          <StatCard icon="trophy-outline" value="A+" label="Last Exam" />
        </Animated.View>
      </LinearGradient>

      {/* Features Grid */}
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
        {/* Error Banner */}
        {error && (
          <View style={styles.errorBanner}>
            <Ionicons name="alert-circle" size={16} color={COLORS.error} />
            <Text style={styles.errorBannerText}>{error}</Text>
          </View>
        )}

        {/* Student Info Card */}
        <View style={styles.infoCard}>
          <View style={styles.infoRow}>
            <View style={styles.infoItem}>
              <Text style={styles.infoLabel}>Admission No</Text>
              <Text style={styles.infoValue}>{student.student_admission}</Text>
            </View>
            <View style={styles.infoItem}>
              <Text style={styles.infoLabel}>Roll Number</Text>
              <Text style={styles.infoValue}>{student.student_roll}</Text>
            </View>
          </View>
          <View style={styles.infoRow}>
            <View style={styles.infoItem}>
              <Text style={styles.infoLabel}>Student Type</Text>
              <Text style={styles.infoValue}>{student.student_type}</Text>
            </View>
            <View style={styles.infoItem}>
              <Text style={styles.infoLabel}>Gender</Text>
              <Text style={styles.infoValue}>{student.student_sex}</Text>
            </View>
          </View>
        </View>

        <View style={styles.featuresContainer}>
          <Text style={styles.sectionTitle}>Quick Access</Text>
          <View style={styles.featuresGrid}>
            {FEATURES.map((feature, index) => (
              <FeatureCard
                key={feature.id}
                feature={feature}
                index={index}
                onPress={() => router.push({
                  pathname: feature.route,
                  params: { 
                    student_id: student.id,
                    studentData: JSON.stringify(student)
                  }
                })}
                fadeAnim={fadeAnim}
              />
            ))}
          </View>
        </View>

        {/* Recent Notices */}
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>
              Recent Notices {notices.length > 0 && `(${notices.length})`}
            </Text>
            <TouchableOpacity onPress={handleViewAllNotices}>
              <Text style={styles.viewAll}>View All</Text>
            </TouchableOpacity>
          </View>
          
          {notices && notices.length > 0 ? (
            notices.slice(0, 3).map((notice) => (
              <NoticeCard
                key={notice.id}
                notice={notice}
                onDownload={handleDownloadAttachment}
                isDownloading={downloadingNoticeId === notice.id}
                formatDate={formatDate}
                stripHtmlTags={stripHtmlTags}
              />
            ))
          ) : (
            <View style={styles.noNoticesContainer}>
              <Ionicons name="notifications-off-outline" size={48} color={COLORS.lightGray} />
              <Text style={styles.noNoticesText}>No notices available</Text>
            </View>
          )}
        </View>

        <View style={{ height: 30 }} />
      </ScrollView>
    </View>
  );
}

function StatCard({ icon, value, label }) {
  return (
    <View style={styles.statCard}>
      <Ionicons name={icon} size={20} color={COLORS.accent} />
      <Text style={styles.statValue}>{value}</Text>
      <Text style={styles.statLabel}>{label}</Text>
    </View>
  );
}

function FeatureCard({ feature, index, onPress, fadeAnim }) {
  const scaleAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    Animated.spring(scaleAnim, {
      toValue: 1,
      delay: index * 50,
      friction: 8,
      tension: 40,
      useNativeDriver: true,
    }).start();
  }, []);

  return (
    <Animated.View
      style={[
        styles.featureCardWrapper,
        {
          opacity: fadeAnim,
          transform: [{ scale: scaleAnim }],
        },
      ]}
    >
      <TouchableOpacity
        style={styles.featureCard}
        onPress={onPress}
        activeOpacity={0.7}
      >
        <LinearGradient
          colors={feature.gradient}
          style={styles.featureGradient}
          start={{ x: 0, y: 0 }}
          end={{ x: 1, y: 1 }}
        >
          <View style={styles.featureIconContainer}>
            <Ionicons name={feature.icon} size={28} color={COLORS.white} />
          </View>
          <Text style={styles.featureTitle}>{feature.title}</Text>
        </LinearGradient>
      </TouchableOpacity>
    </Animated.View>
  );
}

function NoticeCard({ notice, onDownload, isDownloading, formatDate, stripHtmlTags }) {
  const [expanded, setExpanded] = useState(false);
  const hasAttachment = notice.notice_attachment && notice.notice_attachment !== '';
  const description = stripHtmlTags(notice.notice_details);
  const shortDescription = description.length > 100 
    ? description.substring(0, 100) + '...' 
    : description;

  return (
    <View style={styles.noticeCard}>
      <View style={styles.noticeHeader}>
        <View style={styles.noticeIcon}>
          <Ionicons name="megaphone" size={24} color={COLORS.secondary} />
        </View>
        <View style={styles.noticeContent}>
          <Text style={styles.noticeTitle} numberOfLines={2}>
            {notice.notice_title}
          </Text>
          <View style={styles.noticeDateContainer}>
            <Ionicons name="calendar-outline" size={12} color={COLORS.gray} />
            <Text style={styles.noticeDate}>{formatDate(notice.notice_date)}</Text>
          </View>
        </View>
      </View>

      <Text style={styles.noticeDescription}>
        {expanded ? description : shortDescription}
      </Text>

      {description.length > 100 && (
        <TouchableOpacity onPress={() => setExpanded(!expanded)}>
          <Text style={styles.readMoreText}>
            {expanded ? 'Read Less' : 'Read More'}
          </Text>
        </TouchableOpacity>
      )}

      {hasAttachment && (
        <TouchableOpacity
          style={styles.downloadButton}
          onPress={() => onDownload(notice.id, notice.notice_attachment, notice.notice_title)}
          disabled={isDownloading}
        >
          {isDownloading ? (
            <>
              <ActivityIndicator size="small" color={COLORS.white} />
              <Text style={styles.downloadButtonText}>Downloading...</Text>
            </>
          ) : (
            <>
              <Ionicons name="download-outline" size={18} color={COLORS.white} />
              <Text style={styles.downloadButtonText}>Download Attachment</Text>
            </>
          )}
        </TouchableOpacity>
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F5F5F5',
  },
  loadingText: {
    marginTop: 15,
    fontSize: 16,
    color: COLORS.gray,
  },
  errorContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: '#F5F5F5',
    padding: 20,
  },
  errorText: {
    fontSize: 16,
    color: COLORS.error,
    textAlign: 'center',
    marginTop: 15,
    marginBottom: 20,
  },
  retryButton: {
    backgroundColor: COLORS.secondary,
    paddingHorizontal: 30,
    paddingVertical: 12,
    borderRadius: 25,
  },
  retryButtonText: {
    color: COLORS.white,
    fontSize: 16,
    fontWeight: '600',
  },
  errorBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFEBEE',
    padding: 12,
    marginHorizontal: 20,
    marginTop: 15,
    borderRadius: 10,
    gap: 10,
  },
  errorBannerText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.error,
  },
  header: {
    paddingTop: 60,
    paddingBottom: 20,
    paddingHorizontal: 20,
    borderBottomLeftRadius: 30,
    borderBottomRightRadius: 30,
  },
  headerContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    marginBottom: 20,
  },
  headerLeft: {
    flex: 1,
  },
  headerRight: {
    alignItems: 'flex-end',
  },
  welcomeText: {
    fontSize: 14,
    color: COLORS.white,
    opacity: 0.9,
  },
  studentName: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.white,
    marginTop: 4,
  },
  classInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 8,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
    alignSelf: 'flex-start',
  },
  classText: {
    fontSize: 12,
    color: COLORS.white,
    marginLeft: 6,
    fontWeight: '600',
  },
  profileButton: {
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 8,
    
  },
  profileImage: {
    width: 60,
    height: 60,
    borderRadius: 30,
    borderWidth: 3,
    borderColor: COLORS.accent,
  },
  profilePlaceholder: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 3,
    borderColor: COLORS.accent,
  },
  actionButtons: {
    flexDirection: 'row',
    marginTop: 8,
    gap: 8,
  },
  switchButton: {
    backgroundColor: 'rgba(255, 193, 7, 0.3)',
    borderRadius: 15,
    padding: 6,
    width: 26,
    height: 26,
    alignItems: 'center',
    justifyContent: 'center',
  },
  logoutButton: {
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    borderRadius: 15,
    padding: 6,
    width: 26,
    height: 26,
    alignItems: 'center',
    justifyContent: 'center',
  },
  statsContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 12,
  },
  statCard: {
    flex: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    borderRadius: 15,
    padding: 12,
    alignItems: 'center',
  },
  statValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.white,
    marginTop: 4,
  },
  statLabel: {
    fontSize: 11,
    color: COLORS.white,
    opacity: 0.9,
    marginTop: 2,
  },
  scrollView: {
    flex: 1,
  },
  infoCard: {
    backgroundColor: COLORS.white,
    marginHorizontal: 20,
    marginTop: 20,
    borderRadius: 15,
    padding: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  infoRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 12,
  },
  infoItem: {
    flex: 1,
  },
  infoLabel: {
    fontSize: 11,
    color: COLORS.gray,
    marginBottom: 4,
  },
  infoValue: {
    fontSize: 14,
    fontWeight: '700',
    color: COLORS.primary,
  },
  featuresContainer: {
    padding: 20,
  },
  sectionTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 15,
  },
  featuresGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'space-between',
  },
  featureCardWrapper: {
    width: '48%',
    marginBottom: 15,
  },
  featureCard: {
    borderRadius: 20,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.15,
    shadowRadius: 8,
    elevation: 5,
  },
  featureGradient: {
    padding: 20,
    alignItems: 'center',
    minHeight: 120,
    justifyContent: 'center',
  },
  featureIconContainer: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 10,
  },
  featureTitle: {
    fontSize: 13,
    fontWeight: '700',
    color: COLORS.white,
    textAlign: 'center',
  },
  section: {
    paddingHorizontal: 20,
    marginBottom: 20,
  },
  sectionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 15,
  },
  viewAll: {
    fontSize: 14,
    color: COLORS.secondary,
    fontWeight: '600',
  },
  noticeCard: {
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 15,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  noticeHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 10,
  },
  noticeIcon: {
    width: 45,
    height: 45,
    borderRadius: 22.5,
    backgroundColor: '#E8F5E9',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  noticeContent: {
    flex: 1,
  },
  noticeTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: COLORS.primary,
    marginBottom: 6,
  },
  noticeDateContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  noticeDate: {
    fontSize: 12,
    color: COLORS.gray,
  },
  noticeDescription: {
    fontSize: 13,
    color: COLORS.gray,
    lineHeight: 20,
    marginTop: 8,
  },
  readMoreText: {
    fontSize: 13,
    color: COLORS.secondary,
    fontWeight: '600',
    marginTop: 8,
  },
  downloadButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.secondary,
    paddingVertical: 10,
    paddingHorizontal: 15,
    borderRadius: 10,
    marginTop: 12,
    gap: 8,
  },
  downloadButtonText: {
    color: COLORS.white,
    fontSize: 14,
    fontWeight: '600',
  },
  noNoticesContainer: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  noNoticesText: {
    fontSize: 14,
    color: COLORS.gray,
    marginTop: 10,
  },
  actionButtons: {
    flexDirection: 'row',
    marginTop: 10,
    gap: 8,
  },
  switchButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    borderRadius: 20,
    paddingVertical: 6,
    paddingHorizontal: 12,
    gap: 6,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
  },
  switchButtonText: {
    color: COLORS.primary,
    fontSize: 12,
    fontWeight: '600',
  },
  logoutButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(244, 67, 54, 0.9)',
    borderRadius: 20,
    paddingVertical: 6,
    paddingHorizontal: 12,
    gap: 6,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
    elevation: 2,
  },
  logoutButtonText: {
    color: COLORS.white,
    fontSize: 12,
    fontWeight: '600',
  },
});