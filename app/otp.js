import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Animated,
  KeyboardAvoidingView,
  Platform,
  StyleSheet,
  Text,
  TextInput,
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
  error: '#F44336',
  success: '#4CAF50',
};

export default function OTPScreen() {
  const router = useRouter();
  const params = useLocalSearchParams();
  const { phoneNumber, studentCount } = params;

  const [otp, setOtp] = useState(['', '', '', '']);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [timer, setTimer] = useState(60);
  const [canResend, setCanResend] = useState(false);

  const inputRefs = [useRef(), useRef(), useRef(), useRef()];
  const shakeAnim = useRef(new Animated.Value(0)).current;
  const fadeAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    Animated.timing(fadeAnim, {
      toValue: 1,
      duration: 500,
      useNativeDriver: true,
    }).start();

    inputRefs[0].current?.focus();
  }, []);

  useEffect(() => {
    let interval;
    if (timer > 0 && !canResend) {
      interval = setInterval(() => {
        setTimer((prev) => prev - 1);
      }, 1000);
    } else if (timer === 0) {
      setCanResend(true);
    }
    return () => clearInterval(interval);
  }, [timer, canResend]);

  const handleOtpChange = (value, index) => {
    if (!/^\d*$/.test(value)) return;

    const newOtp = [...otp];
    newOtp[index] = value;
    setOtp(newOtp);
    setError('');

    if (value && index < 3) {
      inputRefs[index + 1].current?.focus();
    }

    if (newOtp.every((digit) => digit !== '')) {
      verifyOTP(newOtp.join(''));
    }
  };

  const handleKeyPress = (e, index) => {
    if (e.nativeEvent.key === 'Backspace' && !otp[index] && index > 0) {
      inputRefs[index - 1].current?.focus();
    }
  };

  const shakeAnimation = () => {
    Animated.sequence([
      Animated.timing(shakeAnim, {
        toValue: 10,
        duration: 50,
        useNativeDriver: true,
      }),
      Animated.timing(shakeAnim, {
        toValue: -10,
        duration: 50,
        useNativeDriver: true,
      }),
      Animated.timing(shakeAnim, {
        toValue: 10,
        duration: 50,
        useNativeDriver: true,
      }),
      Animated.timing(shakeAnim, {
        toValue: 0,
        duration: 50,
        useNativeDriver: true,
      }),
    ]).start();
  };

  const storeStudentData = async (studentData) => {
    try {
      await AsyncStorage.setItem('student_id', studentData.id);
      await AsyncStorage.setItem('student_admission', studentData.student_admission);
      await AsyncStorage.setItem('student_name', studentData.student_name);
      await AsyncStorage.setItem('student_class', studentData.student_class);
      await AsyncStorage.setItem('student_section', studentData.student_section);
      await AsyncStorage.setItem('student_mobile', studentData.student_mobile);
      await AsyncStorage.setItem('student_photo', studentData.student_photo);
      await AsyncStorage.setItem('isLoggedIn', 'true');
    } catch (error) {
      console.error('Error storing student data:', error);
    }
  };

  const verifyOTP = async (otpValue) => {
    setLoading(true);
    setError('');

    try {
      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=get_otp',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            student_mobile: phoneNumber,
            otp: otpValue,
          }),
        }
      );

      const result = await response.json();

      if (result.status === 'success') {
        const { count, data, notices } = result;
        await AsyncStorage.setItem('all_students', JSON.stringify(data));
        if (count > 1) {
          // Multiple students - show selection screen
          router.push({
            pathname: '/student-selection',
            params: {
              students: JSON.stringify(data),
              notices: JSON.stringify(notices || []),
            },
          });
        } else if (count === 1) {
          // Single student - store data and navigate to home
          const studentData = data[0];
          await storeStudentData(studentData);

          router.replace({
            pathname: '/student_home',
            params: {
              studentData: JSON.stringify(studentData),
              notices: JSON.stringify(notices || []),
            },
          });
        } else {
          setError('No student found');
          setOtp(['', '', '', '']);
          inputRefs[0].current?.focus();
          shakeAnimation();
        }
      } else {
        setError(result.msg || 'Invalid OTP');
        setOtp(['', '', '', '']);
        inputRefs[0].current?.focus();
        shakeAnimation();
      }
    } catch (err) {
      setError('Network error. Please try again.');
      console.error('OTP Verification Error:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleResendOTP = async () => {
    setTimer(60);
    setCanResend(false);
    setOtp(['', '', '', '']);
    setError('');

    try {
      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=send_otp',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            student_mobile: phoneNumber,
          }),
        }
      );

      const result = await response.json();
      
      if (result.status === 'success') {
        // OTP sent successfully
        console.log('OTP resent successfully');
      }
    } catch (err) {
      console.error('Resend OTP Error:', err);
      setError('Failed to resend OTP. Please try again.');
    }
  };

  return (
    <LinearGradient
      colors={[COLORS.primary, COLORS.secondary]}
      style={styles.container}
    >
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
        style={styles.keyboardView}
      >
        <Animated.View style={[styles.content, { opacity: fadeAnim }]}>
          {/* Back Button */}
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => router.back()}
          >
            <Ionicons name="arrow-back" size={24} color={COLORS.white} />
          </TouchableOpacity>

          {/* Header */}
          <View style={styles.header}>
            <View style={styles.iconContainer}>
              <Ionicons name="lock-closed" size={50} color={COLORS.accent} />
            </View>
            <Text style={styles.title}>Verify OTP</Text>
            <Text style={styles.subtitle}>
              Enter the 4-digit code sent to{'\n'}
              <Text style={styles.phoneText}>+91 {phoneNumber}</Text>
            </Text>
          </View>

          {/* OTP Input */}
          <Animated.View
            style={[
              styles.otpContainer,
              { transform: [{ translateX: shakeAnim }] },
            ]}
          >
            {otp.map((digit, index) => (
              <TextInput
                key={index}
                ref={inputRefs[index]}
                style={[
                  styles.otpInput,
                  digit && styles.otpInputFilled,
                  error && styles.otpInputError,
                ]}
                keyboardType="number-pad"
                maxLength={1}
                value={digit}
                onChangeText={(value) => handleOtpChange(value, index)}
                onKeyPress={(e) => handleKeyPress(e, index)}
                selectTextOnFocus
                editable={!loading}
              />
            ))}
          </Animated.View>

          {/* Error Message */}
          {error ? (
            <View style={styles.errorContainer}>
              <Ionicons name="alert-circle" size={18} color={COLORS.error} />
              <Text style={styles.errorText}>{error}</Text>
            </View>
          ) : null}

          {/* Loading Indicator */}
          {loading && (
            <View style={styles.loadingContainer}>
              <ActivityIndicator size="large" color={COLORS.accent} />
              <Text style={styles.loadingText}>Verifying OTP...</Text>
            </View>
          )}

          {/* Resend OTP */}
          <View style={styles.resendContainer}>
            {!canResend ? (
              <Text style={styles.timerText}>
                Resend OTP in{' '}
                <Text style={styles.timerHighlight}>{timer}s</Text>
              </Text>
            ) : (
              <TouchableOpacity onPress={handleResendOTP}>
                <Text style={styles.resendText}>Resend OTP</Text>
              </TouchableOpacity>
            )}
          </View>

          {/* Student Count Info */}
          {studentCount && parseInt(studentCount) > 1 && (
            <View style={styles.infoBox}>
              <Ionicons name="people-outline" size={20} color={COLORS.accent} />
              <Text style={styles.infoText}>
                {studentCount} students registered with this number
              </Text>
            </View>
          )}

          {/* Help Text */}
          <View style={styles.helpContainer}>
            <Ionicons name="information-circle-outline" size={18} color={COLORS.white} />
            <Text style={styles.helpText}>
              OTP is valid for 10 minutes
            </Text>
          </View>
        </Animated.View>
      </KeyboardAvoidingView>
    </LinearGradient>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  keyboardView: {
    flex: 1,
  },
  content: {
    flex: 1,
    paddingHorizontal: 20,
    paddingTop: 60,
  },
  backButton: {
    width: 45,
    height: 45,
    borderRadius: 22.5,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 30,
  },
  header: {
    alignItems: 'center',
    marginBottom: 50,
  },
  iconContainer: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: COLORS.white,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 10,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: 10,
  },
  subtitle: {
    fontSize: 15,
    color: COLORS.white,
    textAlign: 'center',
    opacity: 0.9,
    lineHeight: 22,
  },
  phoneText: {
    fontWeight: 'bold',
    color: COLORS.accent,
  },
  otpContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 30,
  },
  otpInput: {
    width: 70,
    height: 70,
    borderRadius: 15,
    backgroundColor: COLORS.white,
    fontSize: 28,
    fontWeight: 'bold',
    textAlign: 'center',
    color: COLORS.primary,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.2,
    shadowRadius: 5,
    elevation: 5,
    borderWidth: 2,
    borderColor: 'transparent',
  },
  otpInputFilled: {
    borderColor: COLORS.accent,
    backgroundColor: '#FFFDE7',
  },
  otpInputError: {
    borderColor: COLORS.error,
    backgroundColor: '#FFEBEE',
  },
  errorContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    padding: 15,
    borderRadius: 12,
    marginBottom: 20,
  },
  errorText: {
    color: COLORS.error,
    fontSize: 14,
    marginLeft: 10,
    fontWeight: '600',
    flex: 1,
  },
  loadingContainer: {
    alignItems: 'center',
    marginVertical: 20,
  },
  loadingText: {
    color: COLORS.white,
    fontSize: 14,
    marginTop: 10,
    fontWeight: '500',
  },
  resendContainer: {
    alignItems: 'center',
    marginTop: 20,
  },
  timerText: {
    color: COLORS.white,
    fontSize: 15,
  },
  timerHighlight: {
    fontWeight: 'bold',
    color: COLORS.accent,
  },
  resendText: {
    color: COLORS.accent,
    fontSize: 16,
    fontWeight: 'bold',
    textDecorationLine: 'underline',
  },
  infoBox: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    padding: 15,
    borderRadius: 12,
    marginTop: 30,
  },
  infoText: {
    color: COLORS.white,
    fontSize: 14,
    marginLeft: 10,
    flex: 1,
  },
  helpContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 'auto',
    marginBottom: 30,
    opacity: 0.8,
  },
  helpText: {
    color: COLORS.white,
    fontSize: 13,
    marginLeft: 8,
  },
});