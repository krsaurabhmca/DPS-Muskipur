import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Animated,
  Dimensions,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';

const { width, height } = Dimensions.get('window');

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

export default function LoginScreen() {
  const router = useRouter();
  const [phoneNumber, setPhoneNumber] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(50)).current;
  const logoScale = useRef(new Animated.Value(0)).current;

  useEffect(() => {
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
      Animated.spring(logoScale, {
        toValue: 1,
        friction: 4,
        tension: 40,
        useNativeDriver: true,
      }),
    ]).start();
  }, []);

  const validatePhone = (phone) => {
    const phoneRegex = /^[6-9]\d{9}$/;
    return phoneRegex.test(phone);
  };

  const handleSendOTP = async () => {
    setError('');

    if (!validatePhone(phoneNumber)) {
      setError('Please enter a valid 10-digit mobile number');
      return;
    }

    setLoading(true);

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

      const data = await response.json();

      if (data.status === 'success') {
        router.push({
          pathname: '/otp',
          params: {
            phoneNumber: phoneNumber,
            studentCount: data.count.toString(),
          },
        });
      } else {
        setError(data.msg || 'No student found with this mobile number');
      }
    } catch (err) {
      setError('Network error. Please try again.');
      console.error(err);
    } finally {
      setLoading(false);
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
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
        >
          {/* Logo Section */}
          <Animated.View
            style={[
              styles.logoContainer,
              {
                opacity: fadeAnim,
                transform: [{ scale: logoScale }],
              },
            ]}
          >
            <View style={styles.logoCircle}>
              <Text style={styles.logoText}>DPS</Text>
            </View>
            <Text style={styles.schoolName}>Delhi Public School</Text>
            <Text style={styles.tagline}>Mushkipur</Text>
          </Animated.View>

          {/* Login Form */}
          <Animated.View
            style={[
              styles.formContainer,
              {
                opacity: fadeAnim,
                transform: [{ translateY: slideAnim }],
              },
            ]}
          >
            <View style={styles.formCard}>
              <Text style={styles.welcomeText}>Welcome Back!</Text>
              <Text style={styles.instructionText}>
                Enter your registered mobile number to continue
              </Text>

              {/* Phone Input */}
              <View style={styles.inputContainer}>
                <View style={styles.inputWrapper}>
                  <Ionicons
                    name="phone-portrait-outline"
                    size={24}
                    color={COLORS.primary}
                    style={styles.inputIcon}
                  />
                  <TextInput
                    style={styles.input}
                    placeholder="Mobile Number"
                    placeholderTextColor={COLORS.gray}
                    keyboardType="phone-pad"
                    maxLength={10}
                    value={phoneNumber}
                    onChangeText={(text) => {
                      setPhoneNumber(text);
                      setError('');
                    }}
                  />
                </View>
              </View>

              {/* Error Message */}
              {error ? (
                <View style={styles.errorContainer}>
                  <Ionicons name="alert-circle" size={16} color={COLORS.error} />
                  <Text style={styles.errorText}>{error}</Text>
                </View>
              ) : null}

              {/* Send OTP Button */}
              <TouchableOpacity
                style={[
                  styles.sendOtpButton,
                  loading && styles.sendOtpButtonDisabled,
                ]}
                onPress={handleSendOTP}
                disabled={loading}
                activeOpacity={0.8}
              >
                <LinearGradient
                  colors={[COLORS.accent, '#FFD54F']}
                  style={styles.buttonGradient}
                  start={{ x: 0, y: 0 }}
                  end={{ x: 1, y: 0 }}
                >
                  {loading ? (
                    <ActivityIndicator color={COLORS.primary} size="small" />
                  ) : (
                    <>
                      <Text style={styles.buttonText}>Send OTP</Text>
                      <Ionicons
                        name="arrow-forward"
                        size={20}
                        color={COLORS.primary}
                      />
                    </>
                  )}
                </LinearGradient>
              </TouchableOpacity>

              {/* Info Section */}
              <View style={styles.infoContainer}>
                <Ionicons
                  name="information-circle-outline"
                  size={16}
                  color={COLORS.gray}
                />
                <Text style={styles.infoText}>
                  You will receive a 4-digit OTP on your registered mobile number
                </Text>
              </View>
            </View>
          </Animated.View>

          {/* Footer */}
          <Animated.View style={[styles.footer, { opacity: fadeAnim }]}>
            <TouchableOpacity onPress={() => router.push('/admin_login')}>
            <Text style={styles.footerText}>
              Admin Login
            </Text>
            </TouchableOpacity>
          </Animated.View>
        </ScrollView>
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
  scrollContent: {
    flexGrow: 1,
    paddingVertical: 40,
  },
  logoContainer: {
    alignItems: 'center',
    marginTop: 20,
    marginBottom: 40,
  },
  logoCircle: {
    width: 120,
    height: 120,
    borderRadius: 60,
    backgroundColor: COLORS.accent,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 15,
    borderWidth: 5,
    borderColor: COLORS.white,
  },
  logoText: {
    fontSize: 42,
    fontWeight: '900',
    color: COLORS.primary,
    letterSpacing: 2,
  },
  schoolName: {
    fontSize: 28,
    fontWeight: 'bold',
    color: COLORS.white,
    marginTop: 20,
    textAlign: 'center',
  },
  tagline: {
    fontSize: 16,
    color: COLORS.accent,
    marginTop: 5,
    fontWeight: '600',
  },
  formContainer: {
    paddingHorizontal: 20,
  },
  formCard: {
    backgroundColor: COLORS.white,
    borderRadius: 25,
    padding: 30,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.25,
    shadowRadius: 20,
    elevation: 10,
  },
  welcomeText: {
    fontSize: 28,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 8,
  },
  instructionText: {
    fontSize: 14,
    color: COLORS.gray,
    marginBottom: 30,
    lineHeight: 20,
  },
  inputContainer: {
    marginBottom: 20,
  },
  inputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F5F5F5',
    borderRadius: 15,
    paddingHorizontal: 15,
    borderWidth: 2,
    borderColor: COLORS.lightGray,
  },
  inputIcon: {
    marginRight: 10,
  },
  input: {
    flex: 1,
    height: 55,
    fontSize: 16,
    color: COLORS.primary,
    fontWeight: '600',
  },
  errorContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFEBEE',
    padding: 12,
    borderRadius: 10,
    marginBottom: 15,
  },
  errorText: {
    color: COLORS.error,
    fontSize: 13,
    marginLeft: 8,
    flex: 1,
  },
  sendOtpButton: {
    borderRadius: 15,
    overflow: 'hidden',
    marginTop: 10,
  },
  sendOtpButtonDisabled: {
    opacity: 0.7,
  },
  buttonGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 18,
    gap: 10,
  },
  buttonText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  infoContainer: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginTop: 20,
    padding: 15,
    backgroundColor: '#F9FBE7',
    borderRadius: 10,
  },
  infoText: {
    fontSize: 12,
    color: COLORS.gray,
    marginLeft: 8,
    flex: 1,
    lineHeight: 18,
  },
  footer: {
    alignItems: 'center',
    marginTop: 30,
  },
  footerText: {
    fontSize: 12,
    color: COLORS.white,
    opacity: 0.8,
  },
});