import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import {
  Animated,
  Image,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';

const COLORS = {
  primary: '#1B5E20',
  secondary: '#4CAF50',
  accent: '#FFC107',
  white: '#FFFFFF',
  gray: '#757575',
  lightGray: '#E0E0E0',
};

export default function StudentSelectionScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const [students, setStudents] = useState([]);
  const [notices, setNotices] = useState([]);
  const fadeAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // Parse students
    if (params.students) {
      try {
        const studentsData = JSON.parse(params.students);
        setStudents(studentsData);
        console.log('Students loaded:', studentsData.length);
      } catch (e) {
        console.error('Error parsing students:', e);
      }
    }

    // Parse notices
    if (params.notices) {
      try {
        const noticesData = JSON.parse(params.notices);
        setNotices(noticesData);
        console.log('Notices loaded:', noticesData.length);
      } catch (e) {
        console.error('Error parsing notices:', e);
      }
    }

    // Start animation
    Animated.timing(fadeAnim, {
      toValue: 1,
      duration: 500,
      useNativeDriver: true,
    }).start();
  }, []);

  const handleStudentSelect = async (student) => {
    try {
      // Store all student data in AsyncStorage
      await AsyncStorage.setItem('student_id', student.id);
      await AsyncStorage.setItem('student_admission', student.student_admission);
      await AsyncStorage.setItem('student_name', student.student_name);
      await AsyncStorage.setItem('student_class', student.student_class);
      await AsyncStorage.setItem('student_section', student.student_section);
      await AsyncStorage.setItem('student_roll', student.student_roll);
      await AsyncStorage.setItem('student_type', student.student_type);
      await AsyncStorage.setItem('student_photo', student.student_photo);
      await AsyncStorage.setItem('student_sex', student.student_sex);
      await AsyncStorage.setItem('student_mobile', student.student_mobile);
      await AsyncStorage.setItem('total_paid', student.total_paid);
      await AsyncStorage.setItem('current_dues', student.current_dues);
      await AsyncStorage.setItem('student_data', JSON.stringify(student));
      await AsyncStorage.setItem('isLoggedIn', 'true');

      // Store notices
      if (notices && notices.length > 0) {
        await AsyncStorage.setItem('notices', JSON.stringify(notices));
        console.log('Notices stored in AsyncStorage');
      }

      console.log('Student selected:', student.student_name);

      // Navigate to home with student data and notices
      router.replace({
        pathname: '/student_home',
        params: {
          studentData: JSON.stringify(student),
          notices: JSON.stringify(notices),
        },
      });
    } catch (error) {
      console.error('Error storing student data:', error);
    }
  };

  return (
    <LinearGradient
      colors={[COLORS.primary, COLORS.secondary]}
      style={styles.container}
    >
      <Animated.View style={[styles.content, { opacity: fadeAnim }]}>
        {/* Header */}
        <View style={styles.header}>
          <View style={styles.headerIconContainer}>
            <Ionicons name="people" size={40} color={COLORS.accent} />
          </View>
          <Text style={styles.title}>Select Student</Text>
          <Text style={styles.subtitle}>
            {students.length} student{students.length > 1 ? 's' : ''} found on this number
          </Text>
          {notices.length > 0 && (
            <View style={styles.noticesBadge}>
              <Ionicons name="notifications" size={14} color={COLORS.accent} />
              <Text style={styles.noticesBadgeText}>
                {notices.length} new notice{notices.length > 1 ? 's' : ''}
              </Text>
            </View>
          )}
        </View>

        {/* Students List */}
        <ScrollView
          style={styles.scrollView}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.scrollContent}
        >
          {students.map((student, index) => (
            <Animated.View
              key={student.id}
              style={[
                styles.studentCard,
                {
                  opacity: fadeAnim,
                  transform: [
                    {
                      translateY: fadeAnim.interpolate({
                        inputRange: [0, 1],
                        outputRange: [50 * (index + 1), 0],
                      }),
                    },
                  ],
                },
              ]}
            >
              <TouchableOpacity
                onPress={() => handleStudentSelect(student)}
                activeOpacity={0.7}
              >
                <LinearGradient
                  colors={[COLORS.white, '#F5F5F5']}
                  style={styles.cardGradient}
                >
                  <View style={styles.studentInfo}>
                    {/* Avatar */}
                    <View style={styles.avatarContainer}>
                      {student.student_photo !== 'no_image.jpg' ? (
                        <Image
                          source={{
                            uri: `https://dpsmushkipur.com/bine/upload/${student.student_photo}`,
                          }}
                          style={styles.avatar}
                        />
                      ) : (
                        <View
                          style={[
                            styles.avatarPlaceholder,
                            {
                              backgroundColor:
                                student.student_sex === 'MALE'
                                  ? '#E3F2FD'
                                  : '#FCE4EC',
                            },
                          ]}
                        >
                          <Ionicons
                            name={
                              student.student_sex === 'MALE' ? 'male' : 'female'
                            }
                            size={40}
                            color={
                              student.student_sex === 'MALE'
                                ? '#1976D2'
                                : '#C2185B'
                            }
                          />
                        </View>
                      )}
                      <View style={styles.badge}>
                        <Text style={styles.badgeText}>
                          {student.student_class}
                        </Text>
                      </View>
                    </View>

                    {/* Details */}
                    <View style={styles.details}>
                      <Text style={styles.studentName}>
                        {student.student_name}
                      </Text>
                      <View style={styles.detailRow}>
                        <Ionicons
                          name="school-outline"
                          size={14}
                          color={COLORS.gray}
                        />
                        <Text style={styles.detailText}>
                          Class {student.student_class} - Sec{' '}
                          {student.student_section}
                        </Text>
                      </View>
                      <View style={styles.detailRow}>
                        <Ionicons
                          name="card-outline"
                          size={14}
                          color={COLORS.gray}
                        />
                        <Text style={styles.detailText}>
                          Roll: {student.student_roll} | Adm: {student.student_admission}
                        </Text>
                      </View>
                      <View style={styles.detailRow}>
                        <Ionicons
                          name="bus-outline"
                          size={14}
                          color={COLORS.gray}
                        />
                        <Text style={styles.detailText}>
                          {student.student_type}
                        </Text>
                      </View>
                      
                      {/* Payment Info */}
                      <View style={styles.paymentInfo}>
                        <View style={styles.paymentItem}>
                          <Text style={styles.paymentLabel}>Paid:</Text>
                          <Text style={styles.paymentValuePaid}>₹{student.total_paid}</Text>
                        </View>
                        <View style={styles.paymentDivider} />
                        <View style={styles.paymentItem}>
                          <Text style={styles.paymentLabel}>Due:</Text>
                          <Text style={styles.paymentValueDue}>₹{student.current_dues}</Text>
                        </View>
                      </View>
                    </View>

                    {/* Arrow */}
                    <Ionicons
                      name="chevron-forward"
                      size={24}
                      color={COLORS.accent}
                    />
                  </View>
                </LinearGradient>
              </TouchableOpacity>
            </Animated.View>
          ))}
        </ScrollView>
      </Animated.View>
    </LinearGradient>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  content: {
    flex: 1,
    paddingTop: 60,
  },
  header: {
    paddingHorizontal: 20,
    marginBottom: 30,
    alignItems: 'center',
  },
  headerIconContainer: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 15,
  },
  title: {
    fontSize: 28,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 15,
    color: COLORS.white,
    opacity: 0.9,
  },
  noticesBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
    marginTop: 10,
    gap: 6,
  },
  noticesBadgeText: {
    fontSize: 12,
    color: COLORS.white,
    fontWeight: '600',
  },
  scrollView: {
    flex: 1,
  },
  scrollContent: {
    paddingHorizontal: 20,
    paddingBottom: 100,
  },
  studentCard: {
    marginBottom: 15,
    borderRadius: 20,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.2,
    shadowRadius: 10,
    elevation: 5,
  },
  cardGradient: {
    padding: 15,
  },
  studentInfo: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  avatarContainer: {
    position: 'relative',
    marginRight: 15,
  },
  avatar: {
    width: 70,
    height: 70,
    borderRadius: 35,
    borderWidth: 3,
    borderColor: COLORS.primary,
  },
  avatarPlaceholder: {
    width: 70,
    height: 70,
    borderRadius: 35,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 3,
    borderColor: COLORS.white,
  },
  badge: {
    position: 'absolute',
    bottom: -5,
    right: -5,
    backgroundColor: COLORS.accent,
    borderRadius: 12,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderWidth: 2,
    borderColor: COLORS.white,
  },
  badgeText: {
    fontSize: 11,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  details: {
    flex: 1,
  },
  studentName: {
    fontSize: 17,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 6,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  detailText: {
    fontSize: 12,
    color: COLORS.gray,
    marginLeft: 6,
  },
  paymentInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F0F0F0',
    borderRadius: 8,
    padding: 8,
    marginTop: 8,
  },
  paymentItem: {
    flex: 1,
    alignItems: 'center',
  },
  paymentLabel: {
    fontSize: 10,
    color: COLORS.gray,
    marginBottom: 2,
  },
  paymentValuePaid: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.secondary,
  },
  paymentValueDue: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#F44336',
  },
  paymentDivider: {
    width: 1,
    height: 30,
    backgroundColor: COLORS.lightGray,
  },
  backButtonContainer: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    padding: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
  },
  backButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.white,
    paddingVertical: 15,
    borderRadius: 25,
    gap: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 5,
    elevation: 5,
  },
  backButtonText: {
    color: COLORS.primary,
    fontSize: 16,
    fontWeight: '600',
  },
});