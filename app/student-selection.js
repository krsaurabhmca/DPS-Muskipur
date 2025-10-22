import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useRef } from 'react';
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
  const students = JSON.parse(params.students);
  const fadeAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    Animated.timing(fadeAnim, {
      toValue: 1,
      duration: 500,
      useNativeDriver: true,
    }).start();
  }, []);
  const handleStudentSelect = (student) => {
    AsyncStorage.setItem('student_id', student.id);
    AsyncStorage.setItem('student_name', student.student_name);
    AsyncStorage.setItem('student_class', student.student_class);
    AsyncStorage.setItem('student_section', student.student_section);
    AsyncStorage.setItem('student_roll', student.student_roll);
    AsyncStorage.setItem('total_paid', student.total_paid);
    AsyncStorage.setItem('current_dues', student.current_dues);
    router.replace('./student_home');
  };

  return (
    <LinearGradient
      colors={[COLORS.primary, COLORS.secondary]}
      style={styles.container}
    >
      <Animated.View style={[styles.content, { opacity: fadeAnim }]}>
        {/* Header */}
        <View style={styles.header}>
          <Text style={styles.title}>Select Student</Text>
          <Text style={styles.subtitle}>
            Choose which student you want to view
          </Text>
        </View>

        {/* Students List */}
        <ScrollView
          style={styles.scrollView}
          showsVerticalScrollIndicator={false}
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
                            uri: `https://dpsmushkipur.com/uploads/${student.student_photo}`,
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
                          name="calendar-outline"
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
                          Roll No: {student.student_roll}
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
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: 8,
  },
  subtitle: {
    fontSize: 15,
    color: COLORS.white,
    opacity: 0.9,
  },
  scrollView: {
    flex: 1,
    paddingHorizontal: 20,
  },
  studentCard: {
    marginBottom: 20,
    borderRadius: 20,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.2,
    shadowRadius: 10,
    elevation: 5,
  },
  cardGradient: {
    padding: 20,
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
  },
  avatarPlaceholder: {
    width: 70,
    height: 70,
    borderRadius: 35,
    justifyContent: 'center',
    alignItems: 'center',
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
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 8,
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 4,
  },
  detailText: {
    fontSize: 13,
    color: COLORS.gray,
    marginLeft: 6,
  },
});