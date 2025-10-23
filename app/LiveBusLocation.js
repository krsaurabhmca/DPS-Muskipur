import { Ionicons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useRef } from 'react';
import {
    Animated,
    Dimensions,
    StyleSheet,
    Text,
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
  orange: '#FF9800',
};

export default function LiveBusLocationScreen() {
  const router = useRouter();
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const scaleAnim = useRef(new Animated.Value(0.8)).current;
  const busAnim = useRef(new Animated.Value(0)).current;
  const pulseAnim = useRef(new Animated.Value(1)).current;
  const waveAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // Fade in animation
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 800,
        useNativeDriver: true,
      }),
      Animated.spring(scaleAnim, {
        toValue: 1,
        friction: 5,
        tension: 40,
        useNativeDriver: true,
      }),
    ]).start();

    // Bus moving animation
    Animated.loop(
      Animated.sequence([
        Animated.timing(busAnim, {
          toValue: 1,
          duration: 2000,
          useNativeDriver: true,
        }),
        Animated.timing(busAnim, {
          toValue: 0,
          duration: 2000,
          useNativeDriver: true,
        }),
      ])
    ).start();

    // Pulse animation
    Animated.loop(
      Animated.sequence([
        Animated.timing(pulseAnim, {
          toValue: 1.15,
          duration: 1200,
          useNativeDriver: true,
        }),
        Animated.timing(pulseAnim, {
          toValue: 1,
          duration: 1200,
          useNativeDriver: true,
        }),
      ])
    ).start();

    // Wave animation for location markers
    Animated.loop(
      Animated.sequence([
        Animated.timing(waveAnim, {
          toValue: 1,
          duration: 1500,
          useNativeDriver: true,
        }),
        Animated.timing(waveAnim, {
          toValue: 0,
          duration: 1500,
          useNativeDriver: true,
        }),
      ])
    ).start();
  }, []);

  const busTranslate = busAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [-10, 10],
  });

  const waveOpacity = waveAnim.interpolate({
    inputRange: [0, 0.5, 1],
    outputRange: [0.3, 1, 0.3],
  });

  return (
    <SafeAreaProvider>
      <LinearGradient
        colors={[COLORS.primary, COLORS.secondary]}
        style={styles.container}
      >
        {/* Header */}
        <View style={styles.header}>
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => router.back()}
            activeOpacity={0.7}
          >
            <Ionicons name="arrow-back" size={24} color={COLORS.white} />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Live Bus Location</Text>
          <View style={styles.backButton} />
        </View>

        {/* Content */}
        <Animated.View
          style={[
            styles.content,
            {
              opacity: fadeAnim,
              transform: [{ scale: scaleAnim }],
            },
          ]}
        >
          {/* Animated Icon Container */}
          <Animated.View
            style={[
              styles.iconContainer,
              { transform: [{ scale: pulseAnim }] },
            ]}
          >
            <View style={styles.iconCircle}>
              {/* Animated Location Markers */}
              <Animated.View
                style={[
                  styles.locationMarker,
                  styles.marker1,
                  { opacity: waveOpacity },
                ]}
              >
                <Ionicons name="location" size={20} color={COLORS.accent} />
              </Animated.View>
              <Animated.View
                style={[
                  styles.locationMarker,
                  styles.marker2,
                  { opacity: waveOpacity },
                ]}
              >
                <Ionicons name="location" size={16} color={COLORS.orange} />
              </Animated.View>

              {/* Animated Bus */}
              <Animated.View
                style={{
                  transform: [{ translateX: busTranslate }],
                }}
              >
                <Ionicons name="bus" size={80} color={COLORS.accent} />
              </Animated.View>
            </View>

            {/* Decorative route lines */}
            <View style={styles.routeLine1} />
            <View style={styles.routeLine2} />

            {/* Decorative circles */}
            <View style={[styles.decorativeCircle, styles.circle1]} />
            <View style={[styles.decorativeCircle, styles.circle2]} />
            <View style={[styles.decorativeCircle, styles.circle3]} />
          </Animated.View>

          {/* Coming Soon Badge */}
          <View style={styles.badge}>
            <LinearGradient
              colors={[COLORS.accent, '#FFD54F']}
              style={styles.badgeGradient}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 1 }}
            >
              <Ionicons name="time-outline" size={16} color={COLORS.primary} />
              <Text style={styles.badgeText}>Coming Soon</Text>
            </LinearGradient>
          </View>

          {/* Title */}
          <Text style={styles.title}>Live Bus Tracking</Text>

          {/* Description */}
          <Text style={styles.description}>
            Track your school bus in real-time for added safety and peace of mind.
          </Text>

          {/* Feature List */}
          <View style={styles.featureList}>
            <FeatureItem
              icon="location-outline"
              text="Real-time Bus Location"
              delay={0}
            />
            <FeatureItem
              icon="navigate-outline"
              text="Live Route Tracking"
              delay={100}
            />
            <FeatureItem
              icon="notifications-outline"
              text="Arrival Notifications"
              delay={200}
            />
            <FeatureItem
              icon="speedometer-outline"
              text="Estimated Arrival Time"
              delay={300}
            />
            <FeatureItem
              icon="shield-checkmark-outline"
              text="Safe Journey Monitoring"
              delay={400}
            />
          </View>

          {/* Info Box */}
          <View style={styles.infoBox}>
            <Ionicons
              name="information-circle-outline"
              size={20}
              color={COLORS.accent}
            />
            <Text style={styles.infoText}>
              We're installing GPS tracking systems in all our buses. This feature will be available soon!
            </Text>
          </View>
        </Animated.View>

        {/* Bottom Action */}
        <View style={styles.bottomAction}>
          <TouchableOpacity
            style={styles.notifyButton}
            activeOpacity={0.8}
            onPress={() => {
              alert('You will be notified when live bus tracking is available!');
            }}
          >
            <LinearGradient
              colors={[COLORS.accent, '#FFD54F']}
              style={styles.notifyButtonGradient}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 0 }}
            >
              <Ionicons name="notifications" size={20} color={COLORS.primary} />
              <Text style={styles.notifyButtonText}>Notify Me</Text>
            </LinearGradient>
          </TouchableOpacity>
        </View>
      </LinearGradient>
    </SafeAreaProvider>
  );
}

function FeatureItem({ icon, text, delay }) {
  const fadeAnim = useRef(new Animated.Value(0)).current;
  const slideAnim = useRef(new Animated.Value(20)).current;

  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 600,
        delay: delay,
        useNativeDriver: true,
      }),
      Animated.timing(slideAnim, {
        toValue: 0,
        duration: 600,
        delay: delay,
        useNativeDriver: true,
      }),
    ]).start();
  }, []);

  return (
    <Animated.View
      style={[
        styles.featureItem,
        {
          opacity: fadeAnim,
          transform: [{ translateX: slideAnim }],
        },
      ]}
    >
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
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingTop: 60,
    paddingHorizontal: 20,
    paddingBottom: 20,
  },
  backButton: {
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
  content: {
    flex: 1,
    alignItems: 'center',
    paddingHorizontal: 30,
    paddingTop: 10,
  },
  iconContainer: {
    position: 'relative',
    marginBottom: 30,
  },
  iconCircle: {
    width: 160,
    height: 160,
    borderRadius: 80,
    backgroundColor: 'rgba(255, 255, 255, 0.15)',
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 3,
    borderColor: 'rgba(255, 193, 7, 0.3)',
    overflow: 'visible',
  },
  locationMarker: {
    position: 'absolute',
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  marker1: {
    top: 10,
    right: 20,
  },
  marker2: {
    bottom: 20,
    left: 15,
  },
  routeLine1: {
    position: 'absolute',
    width: 80,
    height: 2,
    backgroundColor: 'rgba(255, 193, 7, 0.3)',
    top: 40,
    left: -20,
    transform: [{ rotate: '45deg' }],
  },
  routeLine2: {
    position: 'absolute',
    width: 60,
    height: 2,
    backgroundColor: 'rgba(255, 193, 7, 0.3)',
    bottom: 50,
    right: -15,
    transform: [{ rotate: '-30deg' }],
  },
  decorativeCircle: {
    position: 'absolute',
    backgroundColor: 'rgba(255, 193, 7, 0.2)',
    borderRadius: 100,
  },
  circle1: {
    width: 40,
    height: 40,
    top: -10,
    right: 10,
  },
  circle2: {
    width: 30,
    height: 30,
    bottom: 20,
    left: -10,
  },
  circle3: {
    width: 25,
    height: 25,
    top: 30,
    left: -15,
  },
  badge: {
    marginBottom: 20,
    borderRadius: 25,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 5,
    elevation: 5,
  },
  badgeGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 10,
    gap: 8,
  },
  badgeText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  title: {
    fontSize: 32,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: 15,
    textAlign: 'center',
  },
  description: {
    fontSize: 16,
    color: COLORS.white,
    textAlign: 'center',
    opacity: 0.9,
    lineHeight: 24,
    marginBottom: 25,
  },
  featureList: {
    width: '100%',
    marginBottom: 20,
  },
  featureItem: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.15)',
    borderRadius: 15,
    padding: 15,
    marginBottom: 12,
    borderLeftWidth: 4,
    borderLeftColor: COLORS.accent,
  },
  featureIconContainer: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255, 193, 7, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  featureText: {
    fontSize: 14,
    color: COLORS.white,
    fontWeight: '600',
    flex: 1,
  },
  infoBox: {
    flexDirection: 'row',
    backgroundColor: 'rgba(255, 193, 7, 0.2)',
    borderRadius: 12,
    padding: 15,
    gap: 10,
    borderWidth: 1,
    borderColor: 'rgba(255, 193, 7, 0.4)',
  },
  infoText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.white,
    lineHeight: 20,
  },
  bottomAction: {
    paddingHorizontal: 30,
    paddingBottom: 40,
  },
  notifyButton: {
    borderRadius: 30,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.3,
    shadowRadius: 10,
    elevation: 8,
  },
  notifyButtonGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 16,
    gap: 10,
  },
  notifyButtonText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
});