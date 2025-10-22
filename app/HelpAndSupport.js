import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
    Alert,
    Linking,
    RefreshControl,
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View
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
  background: '#F5F7FA',
  whatsapp: '#25D366',
  phone: '#2196F3',
  email: '#FF6B6B',
  web: '#9C27B0',
};

export default function HelpSupportScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [supportData, setSupportData] = useState(null);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchSupportData();
  }, []);

  const fetchSupportData = async () => {
    try {
      setLoading(true);
      setError('');

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=help_and_support',
        {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        }
      );

      const data = await response.json();
      
      if (data) {
        setSupportData(data);
        
        // Cache support data
        await AsyncStorage.setItem('cached_support', JSON.stringify(data));
      } else {
        setError('No support information available');
      }
    } catch (err) {
      console.error('Error fetching support data:', err);
      setError('Failed to load support information');
      
      // Try to load cached data
      try {
        const cachedData = await AsyncStorage.getItem('cached_support');
        if (cachedData) {
          setSupportData(JSON.parse(cachedData));
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
    fetchSupportData();
  };

  const handleCall = (phone) => {
    const phoneNumber = phone.replace(/\s/g, '');
    Linking.openURL(`tel:${phoneNumber}`).catch(err => {
      Alert.alert('Error', 'Unable to make a call');
      console.error('Error making call:', err);
    });
  };

  const handleEmail = (email) => {
    Linking.openURL(`mailto:${email}`).catch(err => {
      Alert.alert('Error', 'Unable to open email client');
      console.error('Error opening email:', err);
    });
  };

  const handleWebsite = (website) => {
    let url = website;
    if (!url.startsWith('http://') && !url.startsWith('https://')) {
      url = `https://${url}`;
    }
    Linking.openURL(url).catch(err => {
      Alert.alert('Error', 'Unable to open website');
      console.error('Error opening website:', err);
    });
  };

  const handleWhatsApp = (wpLink) => {
    Linking.openURL(wpLink).catch(err => {
      Alert.alert('Error', 'Unable to open WhatsApp');
      console.error('Error opening WhatsApp:', err);
    });
  };

  const handleMap = (address) => {
    const encodedAddress = encodeURIComponent(address);
    const url = Platform.OS === 'ios' 
      ? `maps://app?q=${encodedAddress}`
      : `geo:0,0?q=${encodedAddress}`;
    
    Linking.openURL(url).catch(() => {
      // Fallback to Google Maps web
      Linking.openURL(`https://www.google.com/maps/search/?api=1&query=${encodedAddress}`);
    });
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <View style={styles.logoCircle}>
          <Ionicons name="help-circle" size={40} color={COLORS.primary} />
        </View>
        <Text style={styles.loadingText}>Loading support info...</Text>
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
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => router.back()}
          >
            <Ionicons name="arrow-back" size={24} color={COLORS.white} />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Help & Support</Text>
          <View style={{ width: 40 }} />
        </View>

        {/* Info Card */}
        <View style={styles.headerCard}>
          <View style={styles.headerIconContainer}>
            <Ionicons name="headset" size={32} color={COLORS.secondary} />
          </View>
          <Text style={styles.headerCardText}>
            We're here to help! Reach out to us anytime
          </Text>
        </View>
      </LinearGradient>

      {/* Error Banner */}
      {error && (
        <View style={styles.errorBanner}>
          <Ionicons name="information-circle" size={16} color="#F57C00" />
          <Text style={styles.errorBannerText}>{error}</Text>
        </View>
      )}

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
        {supportData && (
          <>
            {/* Quick Actions */}
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>
                <Ionicons name="flash" size={18} color={COLORS.accent} /> Quick Actions
              </Text>
              <View style={styles.quickActionsGrid}>
                <QuickActionCard
                  icon="call"
                  label="Call Us"
                  color={COLORS.phone}
                  onPress={() => handleCall(supportData.contact)}
                />
                <QuickActionCard
                  icon="logo-whatsapp"
                  label="WhatsApp"
                  color={COLORS.whatsapp}
                  onPress={() => handleWhatsApp(supportData.wp_channel)}
                />
                <QuickActionCard
                  icon="mail"
                  label="Email"
                  color={COLORS.email}
                  onPress={() => handleEmail(supportData.email)}
                />
                <QuickActionCard
                  icon="globe"
                  label="Website"
                  color={COLORS.web}
                  onPress={() => handleWebsite(supportData.website)}
                />
              </View>
            </View>

            {/* Contact Information */}
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>
                <Ionicons name="information-circle" size={18} color={COLORS.secondary} /> Contact Information
              </Text>

              {/* Phone */}
              <ContactCard
                icon="call"
                iconColor={COLORS.phone}
                title="Phone Number"
                content={supportData.contact}
                onPress={() => handleCall(supportData.contact)}
                actionIcon="call-outline"
                actionLabel="Call Now"
              />

              {/* WhatsApp */}
              <ContactCard
                icon="logo-whatsapp"
                iconColor={COLORS.whatsapp}
                title="WhatsApp Support"
                content="Chat with us on WhatsApp"
                onPress={() => handleWhatsApp(supportData.wp_channel)}
                actionIcon="chatbubbles"
                actionLabel="Open Chat"
              />

              {/* Email */}
              <ContactCard
                icon="mail"
                iconColor={COLORS.email}
                title="Email Address"
                content={supportData.email}
                onPress={() => handleEmail(supportData.email)}
                actionIcon="send"
                actionLabel="Send Email"
              />

              {/* Website */}
              <ContactCard
                icon="globe"
                iconColor={COLORS.web}
                title="Website"
                content={supportData.website}
                onPress={() => handleWebsite(supportData.website)}
                actionIcon="open"
                actionLabel="Visit Website"
              />

              {/* Address */}
              <ContactCard
                icon="location"
                iconColor={COLORS.accent}
                title="School Address"
                content={supportData.address}
                onPress={() => handleMap(supportData.address)}
                actionIcon="navigate"
                actionLabel="Get Directions"
                multiline
              />
            </View>

            {/* Office Hours */}
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>
                <Ionicons name="time" size={18} color={COLORS.secondary} /> Office Hours
              </Text>
              <View style={styles.hoursCard}>
                <View style={styles.hoursRow}>
                  <Ionicons name="sunny" size={20} color={COLORS.accent} />
                  <Text style={styles.hoursDay}>Monday - Friday</Text>
                  <Text style={styles.hoursTime}>8:00 AM - 4:00 PM</Text>
                </View>
                <View style={styles.hoursDivider} />
                <View style={styles.hoursRow}>
                  <Ionicons name="partly-sunny" size={20} color={COLORS.accent} />
                  <Text style={styles.hoursDay}>Saturday</Text>
                  <Text style={styles.hoursTime}>8:00 AM - 1:00 PM</Text>
                </View>
                <View style={styles.hoursDivider} />
                <View style={styles.hoursRow}>
                  <Ionicons name="moon" size={20} color={COLORS.gray} />
                  <Text style={styles.hoursDay}>Sunday</Text>
                  <Text style={[styles.hoursTime, { color: COLORS.error }]}>Closed</Text>
                </View>
              </View>
            </View>

            {/* FAQs */}
            <View style={styles.section}>
              <Text style={styles.sectionTitle}>
                <Ionicons name="help-circle" size={18} color={COLORS.secondary} /> Frequently Asked Questions
              </Text>
              
              <FAQCard
                question="How do I apply for leave?"
                answer="You can apply for leave through the 'Apply Leave' section in the app. Fill in the required details and submit your request."
              />
              
              <FAQCard
                question="How can I check my attendance?"
                answer="Navigate to the Attendance section to view your monthly attendance records with detailed calendar view."
              />
              
              <FAQCard
                question="Where can I view homework assignments?"
                answer="All homework assignments are available in the Homework section, organized by date with downloadable attachments."
              />
              
              <FAQCard
                question="How do I submit a complaint?"
                answer="Use the Complaint section to submit your concerns. Select the appropriate department and describe your issue."
              />
            </View>

            {/* Emergency Contact */}
            <View style={styles.emergencyCard}>
              <LinearGradient
                colors={['#F44336', '#E53935']}
                style={styles.emergencyGradient}
              >
                <Ionicons name="alert-circle" size={32} color={COLORS.white} />
                <View style={styles.emergencyContent}>
                  <Text style={styles.emergencyTitle}>Emergency Contact</Text>
                  <Text style={styles.emergencyText}>
                    For urgent matters, please call us immediately
                  </Text>
                  <TouchableOpacity
                    style={styles.emergencyButton}
                    onPress={() => handleCall(supportData.contact)}
                  >
                    <Ionicons name="call" size={18} color={COLORS.error} />
                    <Text style={styles.emergencyButtonText}>{supportData.contact}</Text>
                  </TouchableOpacity>
                </View>
              </LinearGradient>
            </View>
          </>
        )}
      </ScrollView>
    </View>
  );
}

function QuickActionCard({ icon, label, color, onPress }) {
  return (
    <TouchableOpacity style={styles.quickActionCard} onPress={onPress}>
      <View style={[styles.quickActionIcon, { backgroundColor: color + '20' }]}>
        <Ionicons name={icon} size={28} color={color} />
      </View>
      <Text style={styles.quickActionLabel}>{label}</Text>
    </TouchableOpacity>
  );
}

function ContactCard({ icon, iconColor, title, content, onPress, actionIcon, actionLabel, multiline }) {
  return (
    <View style={styles.contactCard}>
      <View style={[styles.contactIcon, { backgroundColor: iconColor + '20' }]}>
        <Ionicons name={icon} size={24} color={iconColor} />
      </View>
      <View style={styles.contactContent}>
        <Text style={styles.contactTitle}>{title}</Text>
        <Text style={[styles.contactText, multiline && styles.contactTextMultiline]}>
          {content}
        </Text>
      </View>
      <TouchableOpacity style={styles.contactAction} onPress={onPress}>
        <Ionicons name={actionIcon} size={20} color={iconColor} />
      </TouchableOpacity>
    </View>
  );
}

function FAQCard({ question, answer }) {
  const [expanded, setExpanded] = useState(false);

  return (
    <TouchableOpacity
      style={styles.faqCard}
      onPress={() => setExpanded(!expanded)}
      activeOpacity={0.7}
    >
      <View style={styles.faqHeader}>
        <Ionicons 
          name={expanded ? "remove-circle" : "add-circle"} 
          size={24} 
          color={COLORS.secondary} 
        />
        <Text style={styles.faqQuestion}>{question}</Text>
      </View>
      {expanded && (
        <Text style={styles.faqAnswer}>{answer}</Text>
      )}
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
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.background,
  },
  logoCircle: {
    width: 100,
    height: 100,
    borderRadius: 50,
    backgroundColor: COLORS.accent,
    justifyContent: 'center',
    alignItems: 'center',
    borderWidth: 4,
    borderColor: COLORS.primary,
  },
  loadingText: {
    marginTop: 20,
    fontSize: 16,
    color: COLORS.primary,
    fontWeight: '600',
  },
  header: {
    paddingTop: 60,
    paddingBottom: 30,
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
  headerCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    marginHorizontal: 20,
    padding: 15,
    borderRadius: 15,
    gap: 15,
  },
  headerIconContainer: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: COLORS.secondary + '20',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerCardText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.gray,
    lineHeight: 18,
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
  section: {
    paddingHorizontal: 20,
    paddingTop: 25,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 15,
  },
  quickActionsGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 12,
  },
  quickActionCard: {
    width: '48%',
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 20,
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  quickActionIcon: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 12,
  },
  quickActionLabel: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.primary,
  },
  contactCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 15,
    marginBottom: 12,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  contactIcon: {
    width: 50,
    height: 50,
    borderRadius: 25,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  contactContent: {
    flex: 1,
  },
  contactTitle: {
    fontSize: 13,
    color: COLORS.gray,
    marginBottom: 4,
  },
  contactText: {
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.primary,
  },
  contactTextMultiline: {
    lineHeight: 20,
  },
  contactAction: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: COLORS.background,
    justifyContent: 'center',
    alignItems: 'center',
  },
  hoursCard: {
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 20,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  hoursRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 8,
  },
  hoursDay: {
    flex: 1,
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.primary,
    marginLeft: 12,
  },
  hoursTime: {
    fontSize: 14,
    color: COLORS.secondary,
    fontWeight: '500',
  },
  hoursDivider: {
    height: 1,
    backgroundColor: COLORS.lightGray,
    marginVertical: 8,
  },
  faqCard: {
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 15,
    marginBottom: 12,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  faqHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  faqQuestion: {
    flex: 1,
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.primary,
  },
  faqAnswer: {
    fontSize: 14,
    color: COLORS.gray,
    lineHeight: 20,
    marginTop: 12,
    marginLeft: 36,
  },
  emergencyCard: {
    marginHorizontal: 20,
    marginTop: 25,
    marginBottom: 30,
    borderRadius: 20,
    overflow: 'hidden',
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
  },
  emergencyGradient: {
    flexDirection: 'row',
    padding: 20,
    gap: 15,
  },
  emergencyContent: {
    flex: 1,
  },
  emergencyTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: 4,
  },
  emergencyText: {
    fontSize: 13,
    color: COLORS.white,
    opacity: 0.95,
    marginBottom: 12,
  },
  emergencyButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    alignSelf: 'flex-start',
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderRadius: 20,
    gap: 8,
  },
  emergencyButtonText: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.error,
  },
});