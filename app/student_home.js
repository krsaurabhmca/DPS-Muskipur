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
  Linking,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View
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

// Base URL for attachments - adjust this based on your API
const BASE_URL = 'https://dpsmushkipur.com/bine';
const ATTACHMENT_PATHS = {
  notice: '/homework/',    
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
    title: 'Leave Applications',
    icon: 'document-text-outline',
    route: '/StudentLeaves',
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
    title: 'Complaints',
    icon: 'alert-circle-outline',
    route: '/StudentComplaints',
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
  const [downloadProgress, setDownloadProgress] = useState(0);
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

  // ✅ FIXED: Improved download function with multiple path attempts
  const handleDownloadAttachment = async (noticeId, attachment, title) => {
    if (!attachment) {
      Alert.alert('Error', 'No attachment available');
      return;
    }

    setDownloadingNoticeId(noticeId);
    setDownloadProgress(0);

    try {
      // Clean the attachment filename
      const cleanAttachment = attachment.trim();
      
      // Get file extension and create safe filename
      const fileExtension = cleanAttachment.split('.').pop().toLowerCase();
      const safeTitle = title.replace(/[^a-zA-Z0-9\s]/g, '').replace(/\s+/g, '_').substring(0, 50);
      const fileName = `${safeTitle}_${Date.now()}.${fileExtension}`;
      const fileUri = FileSystem.documentDirectory + fileName;

      console.log('Attachment filename:', cleanAttachment);
      console.log('Target file:', fileUri);

      // Try different possible URLs
      const possibleUrls = [
        `${BASE_URL}/homework/${cleanAttachment}`,
      ].filter(Boolean); // Remove null values

      let downloadSuccessful = false;
      let lastError = null;

      for (const url of possibleUrls) {
        try {
          console.log('Trying URL:', url);

          // First, check if the URL is accessible
          const headResponse = await fetch(url, { method: 'HEAD' }).catch(() => null);
          
          if (!headResponse || !headResponse.ok) {
            console.log(`URL not accessible: ${url}`);
            continue;
          }

          console.log('URL accessible, starting download...');

          const downloadResumable = FileSystem.createDownloadResumable(
            url,
            fileUri,
            {
              headers: {
                'Accept': '*/*',
              },
            },
            (progress) => {
              if (progress.totalBytesExpectedToWrite > 0) {
                const percent = Math.round(
                  (progress.totalBytesWritten / progress.totalBytesExpectedToWrite) * 100
                );
                setDownloadProgress(percent);
                console.log(`Download progress: ${percent}%`);
              }
            }
          );

          const result = await downloadResumable.downloadAsync();

          if (result && result.uri) {
            const fileInfo = await FileSystem.getInfoAsync(result.uri);
            console.log('Downloaded file info:', fileInfo);

            if (fileInfo.exists && fileInfo.size > 0) {
              downloadSuccessful = true;
              
              // Share/Open the file
              const sharingAvailable = await Sharing.isAvailableAsync();
              
              if (sharingAvailable) {
                await Sharing.shareAsync(result.uri, {
                  mimeType: getMimeType(fileExtension),
                  dialogTitle: 'Open Attachment',
                  UTI: getUTI(fileExtension),
                });
              } else {
                // Fallback: Try to open with system
                Alert.alert(
                  'Download Complete',
                  `File saved to: ${result.uri}`,
                  [
                    { text: 'OK' },
                    {
                      text: 'Open in Browser',
                      onPress: () => Linking.openURL(url),
                    },
                  ]
                );
              }
              break; // Exit loop on success
            } else {
              // Delete empty/corrupted file
              await FileSystem.deleteAsync(result.uri, { idempotent: true });
              throw new Error('Downloaded file is empty');
            }
          }
        } catch (urlError) {
          console.log(`Failed with URL ${url}:`, urlError.message);
          lastError = urlError;
          continue; // Try next URL
        }
      }

      if (!downloadSuccessful) {
        throw lastError || new Error('Could not download from any URL');
      }

    } catch (error) {
      console.error('Download error:', error);
      
      // Offer to open in browser as fallback
      Alert.alert(
        'Download Failed',
        'Unable to download the attachment. Would you like to try opening it in your browser?',
        [
          { text: 'Cancel', style: 'cancel' },
          {
            text: 'Open in Browser',
            onPress: () => {
              // Try the most likely URL
              const browserUrl = `${BASE_URL}/homework/${attachment}`;
              Linking.openURL(browserUrl).catch(() => {
                Alert.alert('Error', 'Could not open browser');
              });
            },
          },
        ]
      );
    } finally {
      setDownloadingNoticeId(null);
      setDownloadProgress(0);
    }
  };

  // Helper function to get MIME type
  const getMimeType = (extension) => {
    const mimeTypes = {
      pdf: 'application/pdf',
      doc: 'application/msword',
      docx: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      xls: 'application/vnd.ms-excel',
      xlsx: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
      ppt: 'application/vnd.ms-powerpoint',
      pptx: 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
      jpg: 'image/jpeg',
      jpeg: 'image/jpeg',
      png: 'image/png',
      gif: 'image/gif',
      txt: 'text/plain',
      zip: 'application/zip',
      mp3: 'audio/mpeg',
      mp4: 'video/mp4',
    };
    return mimeTypes[extension.toLowerCase()] || 'application/octet-stream';
  };

  // Helper function to get UTI (for iOS)
  const getUTI = (extension) => {
    const utiTypes = {
      pdf: 'com.adobe.pdf',
      doc: 'com.microsoft.word.doc',
      docx: 'org.openxmlformats.wordprocessingml.document',
      jpg: 'public.jpeg',
      jpeg: 'public.jpeg',
      png: 'public.png',
      txt: 'public.plain-text',
    };
    return utiTypes[extension.toLowerCase()] || 'public.data';
  };

  // Alternative download method - open directly in browser
  const handleOpenInBrowser = (attachment) => {
    if (!attachment) return;
    
    const url = `${BASE_URL}/notices/${attachment}`;
    Linking.openURL(url).catch((err) => {
      console.error('Failed to open URL:', err);
      Alert.alert('Error', 'Could not open the attachment');
    });
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
                  <Ionicons name="swap-horizontal" size={16} color={COLORS.primary} />
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
          <StatCard icon="cash-outline" value={`₹${student.total_paid || 0}`} label="Total Paid" />
          <StatCard icon="alert-circle-outline" value={`₹${student.current_dues || 0}`} label="Current Dues" />
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
        {error ? (
          <View style={styles.errorBanner}>
            <Ionicons name="alert-circle" size={16} color={COLORS.error} />
            <Text style={styles.errorBannerText}>{error}</Text>
          </View>
        ) : null}

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
                onOpenInBrowser={handleOpenInBrowser}
                isDownloading={downloadingNoticeId === notice.id}
                downloadProgress={downloadingNoticeId === notice.id ? downloadProgress : 0}
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

// ✅ Updated NoticeCard with better download UI
function NoticeCard({ 
  notice, 
  onDownload, 
  onOpenInBrowser,
  isDownloading, 
  downloadProgress,
  formatDate, 
  stripHtmlTags 
}) {
  const [expanded, setExpanded] = useState(false);
  const hasAttachment = notice.notice_attachment && notice.notice_attachment.trim() !== '';
  const description = stripHtmlTags(notice.notice_details);
  const shortDescription = description.length > 100 
    ? description.substring(0, 100) + '...' 
    : description;

  // Get file extension for icon
  const getFileIcon = (filename) => {
    if (!filename) return 'document-outline';
    const ext = filename.split('.').pop().toLowerCase();
    switch (ext) {
      case 'pdf': return 'document-text';
      case 'doc':
      case 'docx': return 'document';
      case 'jpg':
      case 'jpeg':
      case 'png':
      case 'gif': return 'image';
      case 'mp4':
      case 'avi':
      case 'mov': return 'videocam';
      case 'mp3':
      case 'wav': return 'musical-notes';
      case 'zip':
      case 'rar': return 'archive';
      default: return 'document-outline';
    }
  };

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
        <View style={styles.attachmentContainer}>
          {/* Attachment Info */}
          <View style={styles.attachmentInfo}>
            <Ionicons 
              name={getFileIcon(notice.notice_attachment)} 
              size={20} 
              color={COLORS.secondary} 
            />
            <Text style={styles.attachmentName} numberOfLines={1}>
              {notice.notice_attachment}
            </Text>
          </View>

          {/* Download Buttons */}
          <View style={styles.attachmentButtons}>
            {/* Download Button */}
            {/*<TouchableOpacity
              style={[
                styles.downloadButton,
                isDownloading && styles.downloadButtonDisabled
              ]}
              onPress={() => onDownload(notice.id, notice.notice_attachment, notice.notice_title)}
              disabled={isDownloading}
            >
              {isDownloading ? (
                <View style={styles.downloadingContainer}>
                  <ActivityIndicator size="small" color={COLORS.white} />
                  <Text style={styles.downloadButtonText}>
                    {downloadProgress > 0 ? `${downloadProgress}%` : 'Downloading...'}
                  </Text>
                </View>
              ) : (
                <>
                  <Ionicons name="download-outline" size={18} color={COLORS.white} />
                  <Text style={styles.downloadButtonText}>Download</Text>
                </>
              )}
            </TouchableOpacity> */}

            {/* Open in Browser Button (fallback) */}
            <TouchableOpacity
              style={styles.browserButton}
              onPress={() => onOpenInBrowser(notice.notice_attachment)}
              disabled={isDownloading}
            >
              <Ionicons name="open-outline" size={18} color={COLORS.secondary} />
            </TouchableOpacity>
          </View>

          {/* Progress Bar */}
          {isDownloading && downloadProgress > 0 && (
            <View style={styles.progressBarContainer}>
              <View 
                style={[
                  styles.progressBar, 
                  { width: `${downloadProgress}%` }
                ]} 
              />
            </View>
          )}
        </View>
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
  // ✅ New attachment styles
  attachmentContainer: {
    marginTop: 12,
    padding: 12,
    backgroundColor: '#F5F5F5',
    borderRadius: 10,
  },
  attachmentInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 10,
    gap: 8,
  },
  attachmentName: {
    flex: 1,
    fontSize: 12,
    color: COLORS.gray,
  },
  attachmentButtons: {
    flexDirection: 'row',
    gap: 8,
  },
  downloadButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.secondary,
    paddingVertical: 10,
    paddingHorizontal: 15,
    borderRadius: 10,
    gap: 8,
  },
  downloadButtonDisabled: {
    backgroundColor: COLORS.gray,
  },
  downloadingContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  downloadButtonText: {
    color: COLORS.white,
    fontSize: 14,
    fontWeight: '600',
  },
  browserButton: {
    width: 44,
    height: 44,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: COLORS.secondary,
    justifyContent: 'center',
    alignItems: 'center',
    backgroundColor: COLORS.white,
  },
  progressBarContainer: {
    height: 4,
    backgroundColor: COLORS.lightGray,
    borderRadius: 2,
    marginTop: 10,
    overflow: 'hidden',
  },
  progressBar: {
    height: '100%',
    backgroundColor: COLORS.secondary,
    borderRadius: 2,
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
});