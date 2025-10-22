import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
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
    route: '/student/attendance',
    color: '#4CAF50',
    gradient: ['#4CAF50', '#81C784'],
  },
  {
    id: 'homework',
    title: 'Homework',
    icon: 'book-outline',
    route: '/student/homework',
    color: '#2196F3',
    gradient: ['#2196F3', '#64B5F6'],
  },
  {
    id: 'exam-report',
    title: 'Exam Report',
    icon: 'trophy-outline',
    route: '/student/exam-report',
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
    route: '/student/holiday',
    color: '#9C27B0',
    gradient: ['#9C27B0', '#BA68C8'],
  },
  {
    id: 'leave',
    title: 'Leave Application',
    icon: 'document-text-outline',
    route: '/student/leave-application',
    color: '#00BCD4',
    gradient: ['#00BCD4', '#4DD0E1'],
  },
  {
    id: 'payment',
    title: 'Online Payment',
    icon: 'card-outline',
    route: '/student/online-payment',
    color: '#8BC34A',
    gradient: ['#8BC34A', '#AED581'],
  },
  {
    id: 'payment-history',
    title: 'Payment History',
    icon: 'receipt-outline',
    route: '/student/payment-history',
    color: '#FF5722',
    gradient: ['#FF5722', '#FF8A65'],
  },
  {
    id: 'bus',
    title: 'Live Bus Location',
    icon: 'bus-outline',
    route: '/student/live-bus',
    color: '#795548',
    gradient: ['#795548', '#A1887F'],
  },
  {
    id: 'complain',
    title: 'Complain',
    icon: 'alert-circle-outline',
    route: '/student/complain',
    color: '#F44336',
    gradient: ['#F44336', '#E57373'],
  },
  {
    id: 'help',
    title: 'Help & Support',
    icon: 'help-circle-outline',
    route: '/student/help-support',
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
  const [student, setStudent] = useState(null);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState('');
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(30)).current;

  useEffect(() => {
    fetchStudentProfile();
  }, []);

  const fetchStudentProfile = async () => {
    try {
      setLoading(true);
      setError('');

      // Get student_id from AsyncStorage
      const studentId = await AsyncStorage.getItem('student_id');
      
      if (!studentId) {
        // No student_id found, redirect to login
        router.replace('/index');
        return;
      }

      // Fetch student profile from API
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
        
        // Update AsyncStorage with latest data
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
      
      // Try to load cached data
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
            await AsyncStorage.removeItem('student_id');
            await AsyncStorage.removeItem('student_data');
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
          <View>
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
                        student.student_sex === 'MALE' ? '#64B5F6' : '#F48FB1',
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
            <TouchableOpacity
              style={styles.logoutButton}
              onPress={handleLogout}
            >
              <Ionicons name="log-out-outline" size={18} color={COLORS.white} />
            </TouchableOpacity>
          </View>
        </View>

        {/* Quick Stats */}
        <Animated.View
          style={[
            styles.statsContainer,
            { opacity: fadeAnim, transform: [{ translateY: slideAnim }] },
          ]}
        >
          <StatCard icon="checkbox-outline" value="92%" label="Attendance" />
          <StatCard icon="book-outline" value="5" label="Pending HW" />
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
              <Text style={styles.infoLabel}>Father's Name</Text>
              <Text style={styles.infoValue}>{student.student_father || 'N/A'}</Text>
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
            <Text style={styles.sectionTitle}>Recent Notices</Text>
            <TouchableOpacity onPress={() => router.push('/student/noticeboard')}>
              <Text style={styles.viewAll}>View All</Text>
            </TouchableOpacity>
          </View>
          <NoticeCard
            title="Annual Day Celebration"
            date="10 Jan 2024"
            type="Event"
          />
          <NoticeCard
            title="Winter Break Schedule"
            date="08 Jan 2024"
            type="Holiday"
          />
        </View>
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

function NoticeCard({ title, date, type }) {
  return (
    <TouchableOpacity style={styles.noticeCard} activeOpacity={0.7}>
      <View
        style={[
          styles.noticeIcon,
          { backgroundColor: type === 'Event' ? '#E3F2FD' : '#FFF3E0' },
        ]}
      >
        <Ionicons
          name={type === 'Event' ? 'calendar' : 'sunny'}
          size={24}
          color={type === 'Event' ? '#2196F3' : '#FF9800'}
        />
      </View>
      <View style={styles.noticeContent}>
        <Text style={styles.noticeTitle}>{title}</Text>
        <Text style={styles.noticeDate}>{date}</Text>
      </View>
      <View style={styles.noticeBadge}>
        <Text style={styles.noticeBadgeText}>{type}</Text>
      </View>
    </TouchableOpacity>
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
    borderColor: COLORS.white,
  },
  profilePlaceholder: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 3,
    borderColor: COLORS.white,
  },
  logoutButton: {
    marginTop: 8,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    borderRadius: 15,
    padding: 6,
    alignItems: 'center',
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
    fontSize: 18,
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
    flexDirection: 'row',
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 15,
    marginBottom: 12,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  noticeIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
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
    marginBottom: 4,
  },
  noticeDate: {
    fontSize: 12,
    color: COLORS.gray,
  },
  noticeBadge: {
    backgroundColor: COLORS.accent,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  noticeBadgeText: {
    fontSize: 11,
    fontWeight: '700',
    color: COLORS.primary,
  },
});