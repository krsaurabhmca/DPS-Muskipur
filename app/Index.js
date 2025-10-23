import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Animated,
  Dimensions,
  Image,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';
import { SafeAreaProvider } from 'react-native-safe-area-context';

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
  background: '#F5F5F5',
};

export default function LoginScreen() {
  const router = useRouter();
  const [phoneNumber, setPhoneNumber] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [isFocused, setIsFocused] = useState(false);

  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(50)).current;
  const logoScale = useRef(new Animated.Value(0)).current;
  const logoRotate = useRef(new Animated.Value(0)).current;
  const cardScale = useRef(new Animated.Value(0.9)).current;
  const floatAnim1 = useRef(new Animated.Value(0)).current;
  const floatAnim2 = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // Entry animations
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 1000,
        useNativeDriver: true,
      }),
      Animated.timing(slideAnim, {
        toValue: 0,
        duration: 800,
        useNativeDriver: true,
      }),
      Animated.spring(logoScale, {
        toValue: 1,
        friction: 4,
        tension: 40,
        useNativeDriver: true,
      }),
      Animated.spring(cardScale, {
        toValue: 1,
        friction: 8,
        tension: 40,
        delay: 300,
        useNativeDriver: true,
      }),
    ]).start();

    // Logo subtle rotation
    Animated.loop(
      Animated.sequence([
        Animated.timing(logoRotate, {
          toValue: 1,
          duration: 3000,
          useNativeDriver: true,
        }),
        Animated.timing(logoRotate, {
          toValue: 0,
          duration: 3000,
          useNativeDriver: true,
        }),
      ])
    ).start();

    // Floating animations for decorative elements
    Animated.loop(
      Animated.sequence([
        Animated.timing(floatAnim1, {
          toValue: 1,
          duration: 3000,
          useNativeDriver: true,
        }),
        Animated.timing(floatAnim1, {
          toValue: 0,
          duration: 3000,
          useNativeDriver: true,
        }),
      ])
    ).start();

    Animated.loop(
      Animated.sequence([
        Animated.timing(floatAnim2, {
          toValue: 1,
          duration: 4000,
          useNativeDriver: true,
        }),
        Animated.timing(floatAnim2, {
          toValue: 0,
          duration: 4000,
          useNativeDriver: true,
        }),
      ])
    ).start();
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

  const logoRotateInterpolate = logoRotate.interpolate({
    inputRange: [0, 1],
    outputRange: ['0deg', '5deg'],
  });

  const float1Translate = floatAnim1.interpolate({
    inputRange: [0, 1],
    outputRange: [0, -20],
  });

  const float2Translate = floatAnim2.interpolate({
    inputRange: [0, 1],
    outputRange: [0, 20],
  });

  return (
    <SafeAreaProvider>
      <LinearGradient
        colors={[COLORS.primary, COLORS.secondary, '#66BB6A']}
        style={styles.container}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <KeyboardAvoidingView
          behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
          style={styles.keyboardView}
        >
          <ScrollView
            contentContainerStyle={styles.scrollContent}
            showsVerticalScrollIndicator={false}
            bounces={false}
          >
            {/* Decorative Background Elements */}
            <Animated.View
              style={[
                styles.decorativeCircle1,
                { transform: [{ translateY: float1Translate }] },
              ]}
            />
            <Animated.View
              style={[
                styles.decorativeCircle2,
                { transform: [{ translateY: float2Translate }] },
              ]}
            />

            {/* Logo Section */}
            <Animated.View
              style={[
                styles.logoContainer,
                {
                  opacity: fadeAnim,
                  transform: [
                    { scale: logoScale },
                    { rotate: logoRotateInterpolate },
                  ],
                },
              ]}
            >
              <View style={styles.logoWrapper}>
                <View style={styles.logoCircle}>
                  <Image
                    source={require('./assets/logo.png')}
                    style={styles.logoImage}
                    resizeMode="contain"
                  />
                </View>
                {/* Decorative rings around logo */}
                <View style={styles.logoRing1} />
                <View style={styles.logoRing2} />
              </View>
              <Text style={styles.schoolName}>Delhi Public School</Text>
              <Text style={styles.tagline}>Mushkipur, Khagaria</Text>
              <View style={styles.divider}>
                <View style={styles.dividerLine} />
                <Ionicons name="school" size={20} color={COLORS.accent} />
                <View style={styles.dividerLine} />
              </View>
            </Animated.View>

            {/* Login Form */}
            <Animated.View
              style={[
                styles.formContainer,
                {
                  opacity: fadeAnim,
                  transform: [{ translateY: slideAnim }, { scale: cardScale }],
                },
              ]}
            >
              <View style={styles.formCard}>
                {/* Card Header */}
                <View style={styles.cardHeader}>
                  <LinearGradient
                    colors={[COLORS.primary, COLORS.secondary]}
                    style={styles.cardHeaderGradient}
                    start={{ x: 0, y: 0 }}
                    end={{ x: 1, y: 0 }}
                  >
                    <Ionicons name="log-in" size={24} color={COLORS.accent} />
                    <Text style={styles.welcomeText}>Student Login</Text>
                  </LinearGradient>
                </View>

                <View style={styles.cardBody}>
                  <Text style={styles.instructionText}>
                    Enter your registered mobile number to receive OTP
                  </Text>

                  {/* Phone Input with Country Code */}
                  <View style={styles.inputContainer}>
                    <Text style={styles.inputLabel}>Mobile Number</Text>
                    <View
                      style={[
                        styles.inputWrapper,
                        isFocused && styles.inputWrapperFocused,
                        error && styles.inputWrapperError,
                      ]}
                    >
                      <View style={styles.countryCode}>
                        <Text style={styles.countryCodeText}>🇮🇳 +91</Text>
                      </View>
                      <View style={styles.inputDivider} />
                      <TextInput
                        style={styles.input}
                        placeholder="Enter 10-digit number"
                        placeholderTextColor={COLORS.gray}
                        keyboardType="phone-pad"
                        maxLength={10}
                        value={phoneNumber}
                        onChangeText={(text) => {
                          setPhoneNumber(text.replace(/[^0-9]/g, ''));
                          setError('');
                        }}
                        onFocus={() => setIsFocused(true)}
                        onBlur={() => setIsFocused(false)}
                      />
                      {phoneNumber.length === 10 && !error && (
                        <Ionicons
                          name="checkmark-circle"
                          size={24}
                          color={COLORS.success}
                        />
                      )}
                    </View>
                  </View>

                  {/* Error Message */}
                  {error ? (
                    <Animated.View
                      style={[
                        styles.errorContainer,
                        { opacity: fadeAnim },
                      ]}
                    >
                      <Ionicons name="alert-circle" size={18} color={COLORS.error} />
                      <Text style={styles.errorText}>{error}</Text>
                    </Animated.View>
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
                        <>
                          <ActivityIndicator color={COLORS.primary} size="small" />
                          <Text style={styles.buttonText}>Sending OTP...</Text>
                        </>
                      ) : (
                        <>
                          <Ionicons
                            name="send"
                            size={20}
                            color={COLORS.primary}
                          />
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
                    <View style={styles.infoIconWrapper}>
                      <Ionicons
                        name="shield-checkmark"
                        size={18}
                        color={COLORS.primary}
                      />
                    </View>
                    <Text style={styles.infoText}>
                      Your information is secure. We'll send a 4-digit verification code to your registered mobile number.
                    </Text>
                  </View>
                </View>
              </View>
            </Animated.View>

            {/* Features Section */}
            <Animated.View
              style={[
                styles.featuresContainer,
                { opacity: fadeAnim },
              ]}
            >
              <FeatureItem
                icon="notifications-outline"
                text="Instant Notifications"
                delay={400}
              />
              <FeatureItem
                icon="shield-checkmark-outline"
                text="Secure & Safe"
                delay={500}
              />
              <FeatureItem
                icon="time-outline"
                text="24/7 Access"
                delay={600}
              />
            </Animated.View>

            {/* Footer */}
            <Animated.View style={[styles.footer, { opacity: fadeAnim }]}>
              <TouchableOpacity
                style={styles.adminLoginButton}
                onPress={() => router.push('/admin_login')}
                activeOpacity={0.7}
              >
                <Ionicons name="person-circle-outline" size={18} color={COLORS.white} />
                <Text style={styles.footerText}>Admin Login</Text>
              </TouchableOpacity>
              <Text style={styles.versionText}>Version 1.0.0</Text>
            </Animated.View>
          </ScrollView>
        </KeyboardAvoidingView>
      </LinearGradient>
    </SafeAreaProvider>
  );
}

function FeatureItem({ icon, text, delay }) {
  const fadeAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    Animated.timing(fadeAnim, {
      toValue: 1,
      duration: 600,
      delay: delay,
      useNativeDriver: true,
    }).start();
  }, []);

  return (
    <Animated.View style={[styles.featureItem, { opacity: fadeAnim }]}>
      <View style={styles.featureIconContainer}>
        <Ionicons name={icon} size={20} color={COLORS.accent} />
      </View>
      <Text style={styles.featureText}>{text}</Text>
    </Animated.View>
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
    paddingHorizontal: 20,
  },
  decorativeCircle1: {
    position: 'absolute',
    width: 200,
    height: 200,
    borderRadius: 100,
    backgroundColor: 'rgba(255, 255, 255, 0.1)',
    top: -50,
    right: -50,
  },
  decorativeCircle2: {
    position: 'absolute',
    width: 150,
    height: 150,
    borderRadius: 75,
    backgroundColor: 'rgba(255, 193, 7, 0.1)',
    bottom: 100,
    left: -30,
  },
  logoContainer: {
    alignItems: 'center',
    marginTop: 20,
    marginBottom: 40,
  },
  logoWrapper: {
    position: 'relative',
    marginBottom: 20,
  },
  logoCircle: {
    width: 140,
    height: 140,
    borderRadius: 70,
    backgroundColor: COLORS.white,
    justifyContent: 'center',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.3,
    shadowRadius: 15,
    elevation: 15,
    borderWidth: 5,
    borderColor: COLORS.accent,
  },
  logoImage: {
    width: 110,
    height: 110,
  },
  logoRing1: {
    position: 'absolute',
    width: 160,
    height: 160,
    borderRadius: 80,
    borderWidth: 2,
    borderColor: 'rgba(255, 193, 7, 0.3)',
    top: -10,
    left: -10,
  },
  logoRing2: {
    position: 'absolute',
    width: 180,
    height: 180,
    borderRadius: 90,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.2)',
    top: -20,
    left: -20,
  },
  schoolName: {
    fontSize: 28,
    fontWeight: '900',
    color: COLORS.white,
    textAlign: 'center',
    letterSpacing: 0.5,
    textShadowColor: 'rgba(0, 0, 0, 0.2)',
    textShadowOffset: { width: 0, height: 2 },
    textShadowRadius: 4,
  },
  tagline: {
    fontSize: 16,
    color: COLORS.accent,
    marginTop: 5,
    fontWeight: '700',
    letterSpacing: 1,
  },
  divider: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 15,
    gap: 10,
  },
  dividerLine: {
    width: 40,
    height: 2,
    backgroundColor: 'rgba(255, 193, 7, 0.5)',
  },
  formContainer: {
    marginBottom: 30,
  },
  formCard: {
    backgroundColor: COLORS.white,
    borderRadius: 30,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 15 },
    shadowOpacity: 0.3,
    shadowRadius: 25,
    elevation: 15,
  },
  cardHeader: {
    overflow: 'hidden',
  },
  cardHeaderGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 20,
    gap: 10,
  },
  welcomeText: {
    fontSize: 22,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  cardBody: {
    padding: 25,
  },
  instructionText: {
    fontSize: 14,
    color: COLORS.gray,
    marginBottom: 25,
    lineHeight: 20,
    textAlign: 'center',
  },
  inputContainer: {
    marginBottom: 20,
  },
  inputLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 10,
    marginLeft: 5,
  },
  inputWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.background,
    borderRadius: 15,
    borderWidth: 2,
    borderColor: COLORS.lightGray,
    paddingRight: 15,
    transition: 'all 0.3s',
  },
  inputWrapperFocused: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.white,
    shadowColor: COLORS.primary,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 3,
  },
  inputWrapperError: {
    borderColor: COLORS.error,
    backgroundColor: '#FFEBEE',
  },
  countryCode: {
    paddingHorizontal: 15,
    paddingVertical: 15,
  },
  countryCodeText: {
    fontSize: 16,
    fontWeight: '600',
    color: COLORS.primary,
  },
  inputDivider: {
    width: 1,
    height: 30,
    backgroundColor: COLORS.lightGray,
  },
  input: {
    flex: 1,
    height: 55,
    fontSize: 16,
    color: COLORS.primary,
    fontWeight: '600',
    paddingHorizontal: 15,
  },
  errorContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFEBEE',
    padding: 14,
    borderRadius: 12,
    marginBottom: 15,
    borderLeftWidth: 4,
    borderLeftColor: COLORS.error,
  },
  errorText: {
    color: COLORS.error,
    fontSize: 13,
    marginLeft: 10,
    flex: 1,
    fontWeight: '500',
  },
  sendOtpButton: {
    borderRadius: 15,
    overflow: 'hidden',
    marginTop: 10,
    shadowColor: COLORS.accent,
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 5,
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
    backgroundColor: '#E8F5E9',
    borderRadius: 12,
    borderLeftWidth: 4,
    borderLeftColor: COLORS.primary,
  },
  infoIconWrapper: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: 'rgba(27, 94, 32, 0.1)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 10,
  },
  infoText: {
    flex: 1,
    fontSize: 12,
    color: COLORS.primary,
    lineHeight: 18,
    fontWeight: '500',
  },
  featuresContainer: {
    flexDirection: 'row',
    justifyContent: 'space-around',
    marginBottom: 20,
    paddingHorizontal: 10,
  },
  featureItem: {
    alignItems: 'center',
    flex: 1,
  },
  featureIconContainer: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
    borderWidth: 2,
    borderColor: 'rgba(255, 193, 7, 0.3)',
  },
  featureText: {
    fontSize: 11,
    color: COLORS.white,
    textAlign: 'center',
    fontWeight: '600',
  },
  footer: {
    alignItems: 'center',
    marginTop: 20,
    gap: 10,
  },
  adminLoginButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    paddingHorizontal: 20,
    paddingVertical: 12,
    borderRadius: 25,
    gap: 8,
    borderWidth: 1,
    borderColor: 'rgba(255, 255, 255, 0.3)',
  },
  footerText: {
    fontSize: 14,
    color: COLORS.white,
    fontWeight: '600',
  },
  versionText: {
    fontSize: 11,
    color: COLORS.white,
    opacity: 0.6,
    marginTop: 5,
  },
});