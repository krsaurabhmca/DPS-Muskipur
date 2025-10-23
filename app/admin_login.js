import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import { LinearGradient } from 'expo-linear-gradient';
import { router } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Animated,
  Image,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View
} from 'react-native';
import { SafeAreaProvider } from 'react-native-safe-area-context';

const COLORS = {
  primary: '#6A1B9A',      // Deep Purple
  secondary: '#8E24AA',    // Medium Purple
  accent: '#FFC107',       // Yellow
  lightYellow: '#FFF9C4',
  white: '#FFFFFF',
  gray: '#757575',
  lightGray: '#E0E0E0',
  error: '#F44336',
  success: '#4CAF50',
  background: '#F5F5F5',
};

export default function AdminLogin() {
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [showPassword, setShowPassword] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [isCheckingAuth, setIsCheckingAuth] = useState(true);
  const [error, setError] = useState('');
  const [usernameFocused, setUsernameFocused] = useState(false);
  const [passwordFocused, setPasswordFocused] = useState(false);

  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(50)).current;
  const logoScale = useRef(new Animated.Value(0)).current;
  const logoRotate = useRef(new Animated.Value(0)).current;
  const cardScale = useRef(new Animated.Value(0.9)).current;
  const floatAnim1 = useRef(new Animated.Value(0)).current;
  const floatAnim2 = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    checkLoginStatus();

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

    // Floating animations
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

  const checkLoginStatus = async () => {
    try {
      const isLoggedIn = await AsyncStorage.getItem('isLoggedIn');
      if (isLoggedIn === 'true') {
        router.replace('/Dashboard');
      }
    } catch (error) {
      console.error('Error checking login status:', error);
    } finally {
      setIsCheckingAuth(false);
    }
  };

  const storeUserData = async (userData) => {
    try {
      await AsyncStorage.setItem('user_id', userData.id);
      await AsyncStorage.setItem('full_name', userData.full_name);
      await AsyncStorage.setItem('user_type', userData.user_type);
      await AsyncStorage.setItem('isLoggedIn', 'true');
      console.log('User data stored successfully');
      return true;
    } catch (error) {
      console.error('Error storing user data:', error);
      return false;
    }
  };

  const handleLogin = async () => {
    if (!username.trim() || !password.trim()) {
      setError('Username and password are required');
      return;
    }

    setIsLoading(true);
    setError('');

    try {
      const response = await axios.post(
        'https://dpsmushkipur.com/bine/api.php?task=login',
        {
          user_name: username,
          user_pass: password
        }
      );

      if (response.data.status === 'success' && response.data.count > 0) {
        const userData = response.data.data[0];
        const stored = await storeUserData(userData);
        
        if (stored) {
          router.replace('/Dashboard');
        } else {
          setError('Failed to store login information. Please try again.');
        }
      } else {
        setError('Invalid username or password');
      }
    } catch (err) {
      console.error('Login error:', err);
      setError(
        err.response?.data?.message || 
        'Network error. Please check your connection and try again.'
      );
    } finally {
      setIsLoading(false);
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

  if (isCheckingAuth) {
    return (
      <LinearGradient
        colors={[COLORS.primary, COLORS.secondary, '#AB47BC']}
        style={[styles.container, styles.centerContent]}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <ActivityIndicator size="large" color={COLORS.accent} />
        <Text style={styles.loadingText}>Checking authentication...</Text>
      </LinearGradient>
    );
  }

  return (
    <SafeAreaProvider>
      <LinearGradient
        colors={[COLORS.primary, COLORS.secondary, '#AB47BC']}
        style={styles.container}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <StatusBar style="light" />
        
        <KeyboardAvoidingView 
          behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
          style={styles.keyboardAvoidingView}
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
                <Ionicons name="shield-checkmark" size={20} color={COLORS.accent} />
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
                    <Ionicons name="shield-checkmark" size={24} color={COLORS.accent} />
                    <Text style={styles.welcomeText}>Admin Login</Text>
                  </LinearGradient>
                </View>

                <View style={styles.cardBody}>
                  <Text style={styles.instructionText}>
                    Enter your credentials to access the admin panel
                  </Text>

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

                  {/* Username Input */}
                  <View style={styles.inputWrapper}>
                    <Text style={styles.inputLabel}>Username</Text>
                    <View
                      style={[
                        styles.inputContainer,
                        usernameFocused && styles.inputContainerFocused,
                        error && styles.inputContainerError,
                      ]}
                    >
                      <View style={styles.iconWrapper}>
                        <Ionicons
                          name="person-outline"
                          size={20}
                          color={usernameFocused ? COLORS.primary : COLORS.gray}
                        />
                      </View>
                      <View style={styles.inputDivider} />
                      <TextInput
                        style={styles.input}
                        placeholder="Enter admin username"
                        placeholderTextColor={COLORS.gray}
                        value={username}
                        onChangeText={(text) => {
                          setUsername(text);
                          setError('');
                        }}
                        onFocus={() => setUsernameFocused(true)}
                        onBlur={() => setUsernameFocused(false)}
                        autoCapitalize="none"
                        autoCorrect={false}
                      />
                      {username.length > 0 && !error && (
                        <Ionicons
                          name="checkmark-circle"
                          size={22}
                          color={COLORS.success}
                        />
                      )}
                    </View>
                  </View>

                  {/* Password Input */}
                  <View style={styles.inputWrapper}>
                    <Text style={styles.inputLabel}>Password</Text>
                    <View
                      style={[
                        styles.inputContainer,
                        passwordFocused && styles.inputContainerFocused,
                        error && styles.inputContainerError,
                      ]}
                    >
                      <View style={styles.iconWrapper}>
                        <Ionicons
                          name="lock-closed-outline"
                          size={20}
                          color={passwordFocused ? COLORS.primary : COLORS.gray}
                        />
                      </View>
                      <View style={styles.inputDivider} />
                      <TextInput
                        style={styles.input}
                        placeholder="Enter admin password"
                        placeholderTextColor={COLORS.gray}
                        secureTextEntry={!showPassword}
                        value={password}
                        onChangeText={(text) => {
                          setPassword(text);
                          setError('');
                        }}
                        onFocus={() => setPasswordFocused(true)}
                        onBlur={() => setPasswordFocused(false)}
                        autoCapitalize="none"
                        autoCorrect={false}
                      />
                      <TouchableOpacity 
                        onPress={() => setShowPassword(!showPassword)}
                        style={styles.eyeIcon}
                      >
                        <Ionicons 
                          name={showPassword ? "eye-off-outline" : "eye-outline"} 
                          size={22} 
                          color={COLORS.gray}
                        />
                      </TouchableOpacity>
                    </View>
                  </View>

                  {/* Forgot Password */}
                  {/* <TouchableOpacity style={styles.forgotPasswordContainer}>
                    <Text style={styles.forgotPasswordText}>Forgot Password?</Text>
                  </TouchableOpacity> */}

                  {/* Login Button */}
                  <TouchableOpacity 
                    style={[
                      styles.loginButton,
                      isLoading && styles.loginButtonDisabled,
                    ]}
                    onPress={handleLogin}
                    disabled={isLoading}
                    activeOpacity={0.8}
                  >
                    <LinearGradient
                      colors={[COLORS.accent, '#FFD54F']}
                      style={styles.loginButtonGradient}
                      start={{ x: 0, y: 0 }}
                      end={{ x: 1, y: 0 }}
                    >
                      {isLoading ? (
                        <>
                          <ActivityIndicator color={COLORS.primary} size="small" />
                          <Text style={styles.loginButtonText}>Signing In...</Text>
                        </>
                      ) : (
                        <>
                          <Ionicons
                            name="log-in"
                            size={20}
                            color={COLORS.primary}
                          />
                          <Text style={styles.loginButtonText}>Sign In</Text>
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
                      Your credentials are secured with end-to-end encryption
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
                icon="shield-checkmark-outline"
                text="Secure Access"
                delay={400}
              />
              <FeatureItem
                icon="analytics-outline"
                text="Full Control"
                delay={500}
              />
              <FeatureItem
                icon="people-outline"
                text="Manage Users"
                delay={600}
              />
            </Animated.View>

            {/* Footer */}
            <Animated.View style={[styles.footer, { opacity: fadeAnim }]}>
              <TouchableOpacity
                style={styles.studentLoginButton}
                onPress={() => router.push('/')}
                activeOpacity={0.7}
              >
                <Ionicons name="people-circle-outline" size={18} color={COLORS.white} />
                <Text style={styles.footerText}>I Am Student / Parent</Text>
              </TouchableOpacity>
              {/* <Text style={styles.versionText}>Admin Portal v1.0.0</Text> */}
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
  centerContent: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 15,
    fontSize: 16,
    color: COLORS.white,
    fontWeight: '500',
  },
  keyboardAvoidingView: {
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
  inputWrapper: {
    marginBottom: 20,
  },
  inputLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 10,
    marginLeft: 5,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.background,
    borderRadius: 15,
    borderWidth: 2,
    borderColor: COLORS.lightGray,
    paddingRight: 15,
    transition: 'all 0.3s',
  },
  inputContainerFocused: {
    borderColor: COLORS.primary,
    backgroundColor: COLORS.white,
    shadowColor: COLORS.primary,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 3,
  },
  inputContainerError: {
    borderColor: COLORS.error,
    backgroundColor: '#FFEBEE',
  },
  iconWrapper: {
    paddingHorizontal: 15,
    paddingVertical: 15,
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
  eyeIcon: {
    padding: 5,
  },
  forgotPasswordContainer: {
    alignItems: 'flex-end',
    marginBottom: 20,
  },
  forgotPasswordText: {
    color: COLORS.primary,
    fontSize: 14,
    fontWeight: '600',
  },
  loginButton: {
    borderRadius: 15,
    overflow: 'hidden',
    marginTop: 10,
    shadowColor: COLORS.accent,
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 5,
  },
  loginButtonDisabled: {
    opacity: 0.7,
  },
  loginButtonGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 18,
    gap: 10,
  },
  loginButtonText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  infoContainer: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginTop: 20,
    padding: 15,
    backgroundColor: '#F3E5F5',
    borderRadius: 12,
    borderLeftWidth: 4,
    borderLeftColor: COLORS.primary,
  },
  infoIconWrapper: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: 'rgba(106, 27, 154, 0.1)',
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
  studentLoginButton: {
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