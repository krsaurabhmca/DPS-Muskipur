import { FontAwesome5, Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import * as ImagePicker from 'expo-image-picker';
import { LinearGradient } from 'expo-linear-gradient';
import * as Location from 'expo-location';
import { useRouter } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Dimensions,
  FlatList,
  Image,
  Linking,
  Modal,
  Platform,
  StyleSheet,
  Text,
  TouchableOpacity,
  View
} from 'react-native';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';

const { width, height } = Dimensions.get('window');

export default function SelfieAttendanceScreen() {
  const router = useRouter();

  // Core state variables
  const [userId, setUserId] = useState('');
  const [location, setLocation] = useState(null);
  const [gpsLoading, setGpsLoading] = useState(false);
  const [selfie, setSelfie] = useState(null); // Local image URI
  const [selfieBase64, setSelfieBase64] = useState(''); // Base64 string for upload
  const [historyLogs, setHistoryLogs] = useState([]);
  const [attendanceGrid, setAttendanceGrid] = useState(null);
  const [previewPhotoUrl, setPreviewPhotoUrl] = useState(null);

  // Calendar states
  const [calendarMonth, setCalendarMonth] = useState(new Date().getMonth());
  const [calendarYear, setCalendarYear] = useState(new Date().getFullYear());
  const [selectedCalendarDay, setSelectedCalendarDay] = useState(new Date().getDate());

  // Calendar Logic: Generate days of the month
  const generateCalendarDays = () => {
    const daysInMonth = new Date(calendarYear, calendarMonth + 1, 0).getDate();
    const firstDayIndex = new Date(calendarYear, calendarMonth, 1).getDay(); // Day of week (0-6)

    const daysArray = [];

    // Add empty spacer cells for padding before the 1st of month
    for (let i = 0; i < firstDayIndex; i++) {
      daysArray.push({ id: `spacer-${i}`, spacer: true });
    }

    // Month name strings matching database monthly attendance columns
    const monthNamesShort = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const currentMonthDBString = `${monthNamesShort[calendarMonth]}_${calendarYear}`;

    for (let day = 1; day <= daysInMonth; day++) {
      let status = 'NONE'; // 'PRESENT', 'ABSENT', 'LEAVE', 'NONE'
      let isFuture = new Date(calendarYear, calendarMonth, day) > new Date();

      // Check attendance status in logs/summary
      if (!isFuture) {
        // 1. Check selfie_attendance logs
        const dateString = `${calendarYear}-${String(calendarMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const matchLog = historyLogs.find(log => log.att_date && log.att_date.trim().substring(0, 10) === dateString);

        if (matchLog) {
          status = 'PRESENT';
        } else if (attendanceGrid) {
          // 2. Check standard monthly attendance grid if summary exists
          const lowerDBString = currentMonthDBString.toLowerCase();
          const activeMonthObj = Array.isArray(attendanceGrid) ? attendanceGrid.find(
            item => item.att_month && item.att_month.replace(/\s+/g, '').toLowerCase() === lowerDBString
          ) : (attendanceGrid && attendanceGrid.att_month && attendanceGrid.att_month.replace(/\s+/g, '').toLowerCase() === lowerDBString ? attendanceGrid : null);

          if (activeMonthObj) {
            const dayKey = `d_${day}`;
            const val = activeMonthObj[dayKey];
            if (val === 'P') status = 'PRESENT';
            else if (val === 'A') status = 'ABSENT';
            else if (val === 'L') status = 'LEAVE';
          }
        }
      }

      daysArray.push({
        id: `day-${day}`,
        dayNum: day,
        status,
        isFuture
      });
    }

    return daysArray;
  };

  const changeMonth = (offset) => {
    let newMonth = calendarMonth + offset;
    let newYear = calendarYear;

    if (newMonth < 0) {
      newMonth = 11;
      newYear -= 1;
    } else if (newMonth > 11) {
      newMonth = 0;
      newYear += 1;
    }

    setCalendarMonth(newMonth);
    setCalendarYear(newYear);

    const today = new Date();
    if (newMonth === today.getMonth() && newYear === today.getFullYear()) {
      setSelectedCalendarDay(today.getDate());
    } else {
      setSelectedCalendarDay(null);
    }
  };

  // Render a Single Day Cell inside Calendar
  const renderDayCell = (item) => {
    if (item.spacer) {
      return <View style={styles.calendarDayCellEmpty} key={item.id} />;
    }

    let cellBg = COLORS.white;
    let textColor = COLORS.darkText;
    let badgeText = '';

    if (item.isFuture) {
      textColor = '#CBD5E0';
    } else {
      switch (item.status) {
        case 'PRESENT':
          cellBg = '#E8F5E9';
          textColor = COLORS.success;
          badgeText = 'P';
          break;
        case 'ABSENT':
          cellBg = '#FFEBEE';
          textColor = COLORS.error;
          badgeText = 'A';
          break;
        case 'LEAVE':
          cellBg = '#FFF3E0';
          textColor = COLORS.warning;
          badgeText = 'L';
          break;
        default:
          cellBg = '#F7FAFC';
          textColor = COLORS.grayText;
          break;
      }
    }

    const isSelected = selectedCalendarDay === item.dayNum;

    return (
      <TouchableOpacity
        style={styles.calendarDayCell}
        key={item.id}
        onPress={() => {
          if (!item.isFuture) {
            setSelectedCalendarDay(item.dayNum);
          }
        }}
        disabled={item.isFuture}
        activeOpacity={0.7}
      >
        <View style={[
          styles.dayCircle,
          { backgroundColor: cellBg },
          isSelected && { borderWidth: 1.5, borderColor: COLORS.primary }
        ]}>
          <Text style={[styles.calendarDayNum, { color: textColor, fontWeight: isSelected ? '900' : 'bold' }]}>
            {item.dayNum}
          </Text>
        </View>
        {badgeText ? (
          <View style={[
            styles.statusDot,
            { backgroundColor: item.status === 'PRESENT' ? COLORS.success : item.status === 'ABSENT' ? COLORS.error : COLORS.warning }
          ]}>
            <Text style={styles.statusDotText}>{badgeText}</Text>
          </View>
        ) : null}
      </TouchableOpacity>
    );
  };

  // Helper for timezone-safe local date string (YYYY-MM-DD)
  const getLocalDateStr = () => {
    const d = new Date();
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  // Status and loading states
  const [isInitializing, setIsInitializing] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [activeTab, setActiveTab] = useState('checkin'); // 'checkin' or 'history'
  const [isInRadius, setIsInRadius] = useState(false);
  const [distanceInfo, setDistanceInfo] = useState('');
  const [checkingBoundary, setCheckingBoundary] = useState(false);

  // DPS Theme Colors
  const COLORS = {
    primary: '#1B5E20',     // Premium Green
    secondary: '#4CAF50',   // Vibrant Green
    accent: '#FFC107',      // Yellow/Amber
    white: '#FFFFFF',
    darkText: '#2D3748',
    lightGray: '#F7FAFC',
    border: '#E2E8F0',
    grayText: '#718096',
    success: '#388E3C',
    error: '#D32F2F',
    blueAccent: '#00B0FF',
  };

  // Request Location & Camera Permissions
  const requestPermissions = async () => {
    try {
      // 1. Location permission
      const { status: locStatus } = await Location.requestForegroundPermissionsAsync();
      if (locStatus !== 'granted') {
        Alert.alert('Permission Denied', 'GPS location permission is required to mark your attendance.');
        return false;
      }

      // 2. Camera permission
      const { status: camStatus } = await ImagePicker.requestCameraPermissionsAsync();
      if (camStatus !== 'granted') {
        Alert.alert('Permission Denied', 'Camera permission is required to capture your check-in selfie.');
        return false;
      }
      return true;
    } catch (err) {
      console.error('Permissions request error:', err);
      return false;
    }
  };

  // Check GPS Boundary against admin-configured coordinates via server
  const checkGPSBoundary = async (lat, lng) => {
    setCheckingBoundary(true);
    setDistanceInfo('Verifying school boundary lock...');
    try {
      const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=check_gps_boundary', {
        latitude: String(lat),
        longitude: String(lng)
      });
      if (response.data && response.data.status === 'success') {
        setIsInRadius(response.data.in_radius);
        if (response.data.in_radius) {
          setDistanceInfo(`Within permitted boundary (Distance: ${response.data.distance}m)`);
        } else {
          setDistanceInfo(`Outside school boundary (Distance: ${response.data.distance}m, Allowed: ${response.data.allowed_radius}m)`);
          Alert.alert(
            'Outside School Area',
            `You are outside the school boundary.\n\nDistance: ${response.data.distance}m\nAllowed Radius: ${response.data.allowed_radius}m\n\nAttendance cannot be marked from this location.`,
            [{ text: 'OK' }]
          );
        }
      } else {
        setIsInRadius(true);
        setDistanceInfo('Location locked (Server validation bypassed)');
      }
    } catch (error) {
      console.error('Error checking boundary:', error);
      setIsInRadius(true);
      setDistanceInfo('Location locked (Offline mode)');
    } finally {
      setCheckingBoundary(false);
    }
  };

  // Capture GPS Location
  const captureGPS = async () => {
    setGpsLoading(true);
    setLocation(null);
    setIsInRadius(false);
    setDistanceInfo('');
    try {
      // Verify permissions first
      const hasPermission = await requestPermissions();
      if (!hasPermission) {
        setGpsLoading(false);
        return;
      }

      const loc = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.Balanced,
      });
      setLocation(loc.coords);
      await checkGPSBoundary(loc.coords.latitude, loc.coords.longitude);
    } catch (error) {
      console.error('Error fetching location:', error);
      Alert.alert('GPS Lock Failed', 'Could not lock GPS coordinate signals. Please verify Location Services are turned ON in device settings.');
    } finally {
      setGpsLoading(false);
    }
  };

  // Capture Front Camera Selfie
  const takeSelfie = async () => {
    try {
      const hasPermission = await requestPermissions();
      if (!hasPermission) return;

      const result = await ImagePicker.launchCameraAsync({
        cameraType: ImagePicker.CameraType.front,
        allowsEditing: false,
        quality: 0.5,
        base64: true,
      });

      if (!result.canceled && result.assets && result.assets.length > 0) {
        const photo = result.assets[0];
        setSelfie(photo.uri);
        setSelfieBase64(photo.base64);
      }
    } catch (error) {
      console.error('Error launching camera:', error);
      Alert.alert('Camera Error', 'An error occurred while launching camera.');
    }
  };

  // Fetch past attendance log history
  const fetchHistory = async (uId) => {
    try {
      const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=get_my_attendance', {
        user_id: uId
      });
      if (response.data && response.data.status === 'success') {
        setHistoryLogs(response.data.selfie_logs || []);
        setAttendanceGrid(response.data.attendance || null);
      }
    } catch (error) {
      console.error('Error fetching attendance history:', error);
    }
  };

  // Initialize Screen
  const initializeScreen = async () => {
    try {
      setIsInitializing(true);
      const uId = await AsyncStorage.getItem('user_id');
      if (!uId) {
        Alert.alert('Session Expired', 'Please login again to continue.', [
          { text: 'OK', onPress: () => router.replace('/admin_login') }
        ]);
        return;
      }
      setUserId(uId);
      await fetchHistory(uId);

      // Auto-fetch location permissions and trigger lock on screen mount
      const { status: locStatus } = await Location.requestForegroundPermissionsAsync();
      if (locStatus === 'granted') {
        setTimeout(() => {
          captureGPS();
        }, 100);
      } else {
        Alert.alert('Location Required', 'GPS location permission is required to lock your attendance coordinates.');
      }
    } catch (error) {
      console.error('Error initializing attendance screen:', error);
    } finally {
      setIsInitializing(false);
    }
  };

  useEffect(() => {
    initializeScreen();
  }, []);

  // Helper for timezone-safe time formatting from timestamp
  const formatTimeStr = (tsString) => {
    if (!tsString) return '';
    try {
      const isoStr = tsString.replace(' ', 'T');
      const date = new Date(isoStr);
      if (isNaN(date.getTime())) {
        const date2 = new Date(tsString);
        if (isNaN(date2.getTime())) return tsString;
        return date2.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
      }
      return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    } catch (e) {
      return tsString;
    }
  };

  // Submit Check-In or Check-Out
  const submitSelfieAttendance = async (action = 'checkin') => {
    if (!location) {
      Alert.alert('Missing Location', 'Please capture your GPS coordinates first.');
      return;
    }
    if (!selfieBase64) {
      Alert.alert('Missing Selfie', 'Please capture your front verification selfie first.');
      return;
    }
    if (!isInRadius) {
      Alert.alert('Blocked', `You are outside the school campus boundary. Cannot ${action === 'checkin' ? 'check in' : 'check out'}.`);
      return;
    }

    setIsSubmitting(true);
    try {
      const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=submit_selfie_attendance', {
        user_id: userId,
        latitude: String(location.latitude),
        longitude: String(location.longitude),
        selfie_image: 'data:image/png;base64,' + selfieBase64,
        att_date: getLocalDateStr(),
        action: action
      });

      if (response.data && response.data.status === 'success') {
        Alert.alert('Success', `Your attendance ${action === 'checkin' ? 'check-in' : 'check-out'} has been successfully registered!`, [
          {
            text: 'OK',
            onPress: () => {
              // Reset local check-in inputs
              setSelfie(null);
              setSelfieBase64('');
              setLocation(null);
              setIsInRadius(false);
              setDistanceInfo('');
              // Fetch history again & redirect to history tab
              fetchHistory(userId);
              setActiveTab('history');
            }
          }
        ]);
      } else {
        Alert.alert('Failed', response.data.message || `${action === 'checkin' ? 'Check-in' : 'Check-out'} failed. Please try again.`);
      }
    } catch (error) {
      console.error('Error submitting attendance:', error);
      Alert.alert('Network Error', `Failed to register ${action === 'checkin' ? 'check-in' : 'check-out'}. Please try again later.`);
    } finally {
      setIsSubmitting(false);
    }
  };

  // Open Coordinates in Google Maps
  const openMaps = (lat, lng) => {
    const url = Platform.select({
      ios: `maps:0,0?q=${lat},${lng}`,
      android: `geo:0,0?q=${lat},${lng}`
    });
    Linking.openURL(url).catch(() => {
      // Fallback to web link
      Linking.openURL(`https://www.google.com/maps/search/?api=1&query=${lat},${lng}`);
    });
  };

  // Render Check-In Dashboard View
  const renderCheckInTab = () => {
    const todayStr = getLocalDateStr();
    const todayLog = historyLogs.find(log => log.att_date === todayStr);
    const hasCheckedIn = !!todayLog;
    const hasCheckedOut = todayLog && !!todayLog.checkout_time;

    // Case 1: Both Checked-In and Checked-Out Completed
    if (hasCheckedIn && hasCheckedOut) {
      return (
        <FlatList
          data={[{ key: 'attendance_completed' }]}
          keyExtractor={(item) => item.key}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.tabContentContainer}
          renderItem={() => (
            <View style={styles.workspace}>
              {/* Premium Attendance Completed Today Header Card */}
              <View style={[styles.hudCard, { borderColor: COLORS.success, backgroundColor: '#F0FDF4' }]}>
                <View style={[styles.hudHeader, { borderBottomColor: '#E8F5E9' }]}>
                  <Ionicons name="checkmark-circle" size={24} color={COLORS.success} />
                  <Text style={[styles.hudTitle, { color: COLORS.success, fontSize: 16 }]}>
                    Today's Attendance Completed
                  </Text>
                </View>

                <View style={styles.gpsSuccessView}>
                  <View style={[styles.pingSuccessRing, { backgroundColor: '#DCFCE7' }]}>
                    <Ionicons name="shield-checkmark" size={36} color={COLORS.success} />
                  </View>

                  <Text style={[styles.gpsPlaceholderTitle, { color: COLORS.success, fontSize: 16, marginTop: 4 }]}>
                    Check-In & Check-Out Verified
                  </Text>

                  <Text style={[styles.gpsPlaceholderDesc, { color: '#1E3A1E', fontSize: 13, marginBottom: 8 }]}>
                    Both your check-in and check-out logs have been successfully registered and locked for today, {new Date(todayLog.att_date + 'T00:00:00').toLocaleDateString('en-US', {
                      weekday: 'long',
                      month: 'short',
                      day: 'numeric',
                      year: 'numeric'
                    })}.
                  </Text>
                </View>
              </View>

              {/* Checked-In Details Card */}
              <View style={styles.hudCard}>
                <View style={styles.hudHeader}>
                  <Ionicons name="login-outline" size={20} color={COLORS.primary} />
                  <Text style={styles.hudTitle}>1. Secure Check-In Details</Text>
                </View>

                <View style={styles.checkedInRow}>
                  {/* Selfie Preview */}
                  <View style={styles.checkedInSelfieContainer}>
                    <Image
                      source={{ uri: `https://dpsmushkipur.com/bine/upload/${todayLog.selfie_file}` }}
                      style={styles.checkedInSelfie}
                      defaultSource={require('./assets/default.png')}
                    />
                    <TouchableOpacity
                      style={styles.viewSelfieOverlay}
                      onPress={() => setPreviewPhotoUrl(`https://dpsmushkipur.com/bine/upload/${todayLog.selfie_file}`)}
                    >
                      <Ionicons name="eye" size={16} color="#fff" />
                      <Text style={styles.viewSelfieText}>View</Text>
                    </TouchableOpacity>
                  </View>

                  {/* Coordinates & Details */}
                  <View style={styles.checkedInMeta}>
                    <View style={styles.metaRow}>
                      <Ionicons name="time-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText}>
                        Time: {formatTimeStr(todayLog.created_at)}
                      </Text>
                    </View>
                    <View style={styles.metaRow}>
                      <Ionicons name="location-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText} numberOfLines={1}>
                        Lat: {parseFloat(todayLog.latitude).toFixed(6)}
                      </Text>
                    </View>
                    <View style={styles.metaRow}>
                      <Ionicons name="location-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText} numberOfLines={1}>
                        Long: {parseFloat(todayLog.longitude).toFixed(6)}
                      </Text>
                    </View>

                    <TouchableOpacity
                      style={[styles.mapsLinkBtn, { marginTop: 10, paddingVertical: 6, paddingHorizontal: 12, backgroundColor: '#F0FDF4', borderRadius: 8, alignSelf: 'flex-start' }]}
                      onPress={() => openMaps(todayLog.latitude, todayLog.longitude)}
                    >
                      <Ionicons name="map-outline" size={14} color={COLORS.primary} style={{ marginRight: 6 }} />
                      <Text style={styles.mapsLinkText}>Open in Maps</Text>
                    </TouchableOpacity>
                  </View>
                </View>
              </View>

              {/* Checked-Out Details Card */}
              <View style={styles.hudCard}>
                <View style={styles.hudHeader}>
                  <Ionicons name="logout-outline" size={20} color={COLORS.secondary} />
                  <Text style={styles.hudTitle}>2. Secure Check-Out Details</Text>
                </View>

                <View style={styles.checkedInRow}>
                  {/* Selfie Preview */}
                  <View style={styles.checkedInSelfieContainer}>
                    <Image
                      source={{ uri: `https://dpsmushkipur.com/bine/upload/${todayLog.checkout_file}` }}
                      style={styles.checkedInSelfie}
                      defaultSource={require('./assets/default.png')}
                    />
                    <TouchableOpacity
                      style={styles.viewSelfieOverlay}
                      onPress={() => setPreviewPhotoUrl(`https://dpsmushkipur.com/bine/upload/${todayLog.checkout_file}`)}
                    >
                      <Ionicons name="eye" size={16} color="#fff" />
                      <Text style={styles.viewSelfieText}>View</Text>
                    </TouchableOpacity>
                  </View>

                  {/* Coordinates & Details */}
                  <View style={styles.checkedInMeta}>
                    <View style={styles.metaRow}>
                      <Ionicons name="time-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText}>
                        Time: {formatTimeStr(todayLog.checkout_time)}
                      </Text>
                    </View>
                    <View style={styles.metaRow}>
                      <Ionicons name="location-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText} numberOfLines={1}>
                        Lat: {parseFloat(todayLog.checkout_latitude).toFixed(6)}
                      </Text>
                    </View>
                    <View style={styles.metaRow}>
                      <Ionicons name="location-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText} numberOfLines={1}>
                        Long: {parseFloat(todayLog.checkout_longitude).toFixed(6)}
                      </Text>
                    </View>

                    <TouchableOpacity
                      style={[styles.mapsLinkBtn, { marginTop: 10, paddingVertical: 6, paddingHorizontal: 12, backgroundColor: '#F0FDF4', borderRadius: 8, alignSelf: 'flex-start' }]}
                      onPress={() => openMaps(todayLog.checkout_latitude, todayLog.checkout_longitude)}
                    >
                      <Ionicons name="map-outline" size={14} color={COLORS.primary} style={{ marginRight: 6 }} />
                      <Text style={styles.mapsLinkText}>Open in Maps</Text>
                    </TouchableOpacity>
                  </View>
                </View>
              </View>
            </View>
          )}
        />
      );
    }

    // Case 2: Checked In, but Check-Out Pending
    if (hasCheckedIn && !hasCheckedOut) {
      return (
        <FlatList
          data={[{ key: 'checkout_pending' }]}
          keyExtractor={(item) => item.key}
          showsVerticalScrollIndicator={false}
          contentContainerStyle={styles.tabContentContainer}
          renderItem={() => (
            <View style={styles.workspace}>
              {/* Premium Check-In Active Header Card */}
              <View style={[styles.hudCard, { borderColor: COLORS.primary, backgroundColor: '#F0FDF4' }]}>
                <View style={[styles.hudHeader, { borderBottomColor: '#E8F5E9' }]}>
                  <Ionicons name="checkmark-circle" size={24} color={COLORS.success} />
                  <Text style={[styles.hudTitle, { color: COLORS.success, fontSize: 16 }]}>
                    Today's Check-In Completed
                  </Text>
                </View>

                <View style={styles.checkedInRow}>
                  {/* Selfie Preview */}
                  <View style={styles.checkedInSelfieContainer}>
                    <Image
                      source={{ uri: `https://dpsmushkipur.com/bine/upload/${todayLog.selfie_file}` }}
                      style={styles.checkedInSelfie}
                      defaultSource={require('./assets/default.png')}
                    />
                    <TouchableOpacity
                      style={styles.viewSelfieOverlay}
                      onPress={() => setPreviewPhotoUrl(`https://dpsmushkipur.com/bine/upload/${todayLog.selfie_file}`)}
                    >
                      <Ionicons name="eye" size={16} color="#fff" />
                      <Text style={styles.viewSelfieText}>View</Text>
                    </TouchableOpacity>
                  </View>

                  {/* Coordinates & Details */}
                  <View style={styles.checkedInMeta}>
                    <View style={styles.metaRow}>
                      <Ionicons name="time-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText}>
                        Checked In: {formatTimeStr(todayLog.created_at)}
                      </Text>
                    </View>
                    <View style={styles.metaRow}>
                      <Ionicons name="location-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText} numberOfLines={1}>
                        Lat: {parseFloat(todayLog.latitude).toFixed(6)}
                      </Text>
                    </View>
                    <View style={styles.metaRow}>
                      <Ionicons name="location-outline" size={16} color={COLORS.grayText} />
                      <Text style={styles.metaText} numberOfLines={1}>
                        Long: {parseFloat(todayLog.longitude).toFixed(6)}
                      </Text>
                    </View>

                    <TouchableOpacity
                      style={[styles.mapsLinkBtn, { marginTop: 10, paddingVertical: 6, paddingHorizontal: 12, backgroundColor: '#F0FDF4', borderRadius: 8, alignSelf: 'flex-start' }]}
                      onPress={() => openMaps(todayLog.latitude, todayLog.longitude)}
                    >
                      <Ionicons name="map-outline" size={14} color={COLORS.primary} style={{ marginRight: 6 }} />
                      <Text style={styles.mapsLinkText}>Open in Maps</Text>
                    </TouchableOpacity>
                  </View>
                </View>
              </View>

              {/* Secure Check-Out Portal */}
              <View style={[styles.hudCard, { borderColor: COLORS.secondary }]}>
                <View style={styles.hudHeader}>
                  <Ionicons name="log-out" size={20} color={COLORS.secondary} />
                  <Text style={[styles.hudTitle, { color: COLORS.secondary }]}>Secure Daily Check-Out</Text>
                </View>

                {location ? (
                  <View style={styles.gpsSuccessView}>
                    {checkingBoundary ? (
                      <View style={styles.pingSuccessRing}>
                        <ActivityIndicator size="large" color={COLORS.secondary} />
                      </View>
                    ) : (
                      <View style={[styles.pingSuccessRing, { backgroundColor: isInRadius ? '#E8F5E9' : '#FFEBEE' }]}>
                        <Ionicons
                          name={isInRadius ? "checkmark-circle" : "close-circle"}
                          size={42}
                          color={isInRadius ? COLORS.success : COLORS.error}
                        />
                      </View>
                    )}

                    <Text style={styles.gpsCoordText}>
                      Lat: {parseFloat(location.latitude).toFixed(6)} | Long: {parseFloat(location.longitude).toFixed(6)}
                    </Text>

                    {checkingBoundary ? (
                      <Text style={[styles.gpsPrecisionText, { color: COLORS.secondary }]}>
                        Verifying location boundary...
                      </Text>
                    ) : (
                      <Text style={[styles.gpsPrecisionText, { color: isInRadius ? COLORS.success : COLORS.error }]}>
                        {distanceInfo}
                      </Text>
                    )}

                    <TouchableOpacity style={styles.recalBtn} onPress={captureGPS} disabled={gpsLoading || checkingBoundary}>
                      <Text style={styles.recalBtnText}>Refresh GPS Location</Text>
                    </TouchableOpacity>
                  </View>
                ) : (
                  <View style={styles.gpsLockView}>
                    <View style={[styles.pulseContainer, { borderColor: '#E8F5E9' }]}>
                      {gpsLoading ? (
                        <ActivityIndicator size="large" color={COLORS.secondary} />
                      ) : (
                        <Ionicons name="navigate" size={32} color={COLORS.grayText} />
                      )}
                    </View>
                    <Text style={styles.gpsPlaceholderTitle}>
                      {gpsLoading ? 'Acquiring GPS Signal Satellite...' : 'Check-Out GPS Lock'}
                    </Text>
                    <Text style={styles.gpsPlaceholderDesc}>
                      To check out, please secure a coordinate lock within the campus area.
                    </Text>
                    <TouchableOpacity
                      style={[styles.captureBtn, { backgroundColor: COLORS.secondary }]}
                      onPress={captureGPS}
                      disabled={gpsLoading}
                    >
                      <Text style={styles.captureBtnText}>
                        {gpsLoading ? 'Locking Satellite...' : 'Acquire GPS Signal'}
                      </Text>
                    </TouchableOpacity>
                  </View>
                )}
              </View>

              {location && !checkingBoundary && isInRadius && (
                <View style={styles.hudCard}>
                  <View style={styles.hudHeader}>
                    <Ionicons name="camera" size={20} color={COLORS.blueAccent} />
                    <Text style={styles.hudTitle}>Check-Out Facial Selfie</Text>
                  </View>

                  {selfie ? (
                    <View style={styles.selfieSuccessView}>
                      <View style={styles.selfiePreviewContainer}>
                        <Image source={{ uri: selfie }} style={styles.selfiePreview} />
                      </View>
                      <Text style={styles.selfieSuccessTitle}>Face Image Captured</Text>
                      <TouchableOpacity style={styles.recalBtn} onPress={takeSelfie}>
                        <Text style={styles.recalBtnText}>Retake Selfie</Text>
                      </TouchableOpacity>
                    </View>
                  ) : (
                    <View style={styles.gpsLockView}>
                      <View style={[styles.pulseContainer, { borderColor: '#E0F7FA' }]}>
                        <Ionicons name="image-outline" size={32} color={COLORS.grayText} />
                      </View>
                      <Text style={styles.gpsPlaceholderTitle}>Facial Photo Verification</Text>
                      <Text style={styles.gpsPlaceholderDesc}>
                        Please capture a front-facing selfie to verify your identity for Check-Out.
                      </Text>
                      <TouchableOpacity
                        style={[styles.captureBtn, { backgroundColor: COLORS.blueAccent }]}
                        onPress={takeSelfie}
                      >
                        <Text style={styles.captureBtnText}>Capture Front Selfie</Text>
                      </TouchableOpacity>
                    </View>
                  )}
                </View>
              )}

              {location && !checkingBoundary && !isInRadius && (
                <View style={[styles.hudCard, { borderColor: COLORS.error, backgroundColor: '#FFF5F5' }]}>
                  <View style={styles.hudHeader}>
                    <Ionicons name="alert-circle" size={20} color={COLORS.error} />
                    <Text style={[styles.hudTitle, { color: COLORS.error }]}>Check-Out Blocked</Text>
                  </View>
                  <View style={styles.gpsLockView}>
                    <Ionicons name="close-circle" size={48} color={COLORS.error} style={{ marginBottom: 12 }} />
                    <Text style={[styles.gpsPlaceholderTitle, { color: COLORS.error }]}>Outside School Area</Text>
                    <Text style={styles.gpsPlaceholderDesc}>
                      Check-outs are restricted to the school campus boundary. You are currently out of range.
                    </Text>
                  </View>
                </View>
              )}

              {location && !checkingBoundary && isInRadius && (
                <TouchableOpacity
                  style={[
                    styles.submitBigBtn,
                    { backgroundColor: COLORS.secondary, shadowColor: COLORS.secondary },
                    (!location || !selfieBase64) && styles.disabledSubmitBtn,
                    isSubmitting && { opacity: 0.8 }
                  ]}
                  onPress={() => submitSelfieAttendance('checkout')}
                  disabled={!location || !selfieBase64 || isSubmitting}
                >
                  {isSubmitting ? (
                    <ActivityIndicator size="small" color="#fff" />
                  ) : (
                    <>
                      <Ionicons name="shield-checkmark" size={22} color="#fff" style={{ marginRight: 8 }} />
                      <Text style={[styles.submitBigBtnText, { color: '#fff' }]}>Verify & Check Out</Text>
                    </>
                  )}
                </TouchableOpacity>
              )}
            </View>
          )}
        />
      );
    }

    // Case 3: Default Check-In required (No log today yet)
    return (
      <FlatList
        data={[{ key: 'checkin_workspace' }]}
        keyExtractor={(item) => item.key}
        showsVerticalScrollIndicator={false}
        contentContainerStyle={styles.tabContentContainer}
        renderItem={() => (
          <View style={styles.workspace}>
            {/* GPS HUD RADAR */}
            <View style={styles.hudCard}>
              <View style={styles.hudHeader}>
                <Ionicons name="location" size={20} color={COLORS.primary} />
                <Text style={styles.hudTitle}>GPS Coordinate Lock</Text>
              </View>

              {location ? (
                <View style={styles.gpsSuccessView}>
                  {checkingBoundary ? (
                    <View style={styles.pingSuccessRing}>
                      <ActivityIndicator size="large" color={COLORS.primary} />
                    </View>
                  ) : (
                    <View style={[styles.pingSuccessRing, { backgroundColor: isInRadius ? '#E8F5E9' : '#FFEBEE' }]}>
                      <Ionicons
                        name={isInRadius ? "checkmark-circle" : "close-circle"}
                        size={42}
                        color={isInRadius ? COLORS.success : COLORS.error}
                      />
                    </View>
                  )}

                  <Text style={styles.gpsCoordText}>
                    Lat: {parseFloat(location.latitude).toFixed(6)} | Long: {parseFloat(location.longitude).toFixed(6)}
                  </Text>

                  {checkingBoundary ? (
                    <Text style={[styles.gpsPrecisionText, { color: COLORS.primary }]}>
                      Verifying location boundary...
                    </Text>
                  ) : (
                    <Text style={[styles.gpsPrecisionText, { color: isInRadius ? COLORS.success : COLORS.error }]}>
                      {distanceInfo}
                    </Text>
                  )}

                  <TouchableOpacity style={styles.recalBtn} onPress={captureGPS} disabled={gpsLoading || checkingBoundary}>
                    <Text style={styles.recalBtnText}>Refresh GPS Location</Text>
                  </TouchableOpacity>
                </View>
              ) : (
                <View style={styles.gpsLockView}>
                  <View style={styles.pulseContainer}>
                    {gpsLoading ? (
                      <ActivityIndicator size="large" color={COLORS.primary} />
                    ) : (
                      <Ionicons name="navigate" size={32} color={COLORS.grayText} />
                    )}
                  </View>
                  <Text style={styles.gpsPlaceholderTitle}>
                    {gpsLoading ? 'Acquiring GPS Signal Satellite...' : 'GPS Lock Required'}
                  </Text>
                  <Text style={styles.gpsPlaceholderDesc}>
                    Your daily check-in is verified through real-time GPS boundary locks. Click below to secure a coordinate lock.
                  </Text>
                  <TouchableOpacity
                    style={[styles.captureBtn, { backgroundColor: COLORS.primary }]}
                    onPress={captureGPS}
                    disabled={gpsLoading}
                  >
                    <Text style={styles.captureBtnText}>
                      {gpsLoading ? 'Locking Satellite...' : 'Acquire GPS Signal'}
                    </Text>
                  </TouchableOpacity>
                </View>
              )}
            </View>

            {/* If in radius, show option to mark attendance, else show blocked UI */}
            {location && !checkingBoundary ? (
              isInRadius ? (
                <>
                  {/* Selfie Capture HUD */}
                  <View style={styles.hudCard}>
                    <View style={styles.hudHeader}>
                      <Ionicons name="camera" size={20} color={COLORS.blueAccent} />
                      <Text style={styles.hudTitle}>Check-In Facial Selfie</Text>
                    </View>

                    {selfie ? (
                      <View style={styles.selfieSuccessView}>
                        <View style={styles.selfiePreviewContainer}>
                          <Image source={{ uri: selfie }} style={styles.selfiePreview} />
                        </View>
                        <Text style={styles.selfieSuccessTitle}>Face Image Captured</Text>
                        <TouchableOpacity style={styles.recalBtn} onPress={takeSelfie}>
                          <Text style={styles.recalBtnText}>Retake Selfie</Text>
                        </TouchableOpacity>
                      </View>
                    ) : (
                      <View style={styles.gpsLockView}>
                        <View style={[styles.pulseContainer, { borderColor: '#E0F7FA' }]}>
                          <Ionicons name="image-outline" size={32} color={COLORS.grayText} />
                        </View>
                        <Text style={styles.gpsPlaceholderTitle}>Facial Photo Verification</Text>
                        <Text style={styles.gpsPlaceholderDesc}>
                          Please capture a front-facing selfie to verify your check-in identity before submitting.
                        </Text>
                        <TouchableOpacity
                          style={[styles.captureBtn, { backgroundColor: COLORS.blueAccent }]}
                          onPress={takeSelfie}
                        >
                          <Text style={styles.captureBtnText}>Capture Front Selfie</Text>
                        </TouchableOpacity>
                      </View>
                    )}
                  </View>

                  {/* Big Action Submit Button */}
                  <TouchableOpacity
                    style={[
                      styles.submitBigBtn,
                      (!location || !selfieBase64) && styles.disabledSubmitBtn,
                      isSubmitting && { opacity: 0.8 }
                    ]}
                    onPress={() => submitSelfieAttendance('checkin')}
                    disabled={!location || !selfieBase64 || isSubmitting}
                  >
                    {isSubmitting ? (
                      <ActivityIndicator size="small" color="#fff" />
                    ) : (
                      <>
                        <Ionicons name="shield-checkmark" size={22} color="#fff" style={{ marginRight: 8 }} />
                        <Text style={styles.submitBigBtnText}>Verify & Mark Present</Text>
                      </>
                    )}
                  </TouchableOpacity>
                </>
              ) : (
                /* OUT OF SCHOOL AREA BLOCKED PANEL */
                <View style={[styles.hudCard, { borderColor: COLORS.error, backgroundColor: '#FFF5F5' }]}>
                  <View style={styles.hudHeader}>
                    <Ionicons name="alert-circle" size={20} color={COLORS.error} />
                    <Text style={[styles.hudTitle, { color: COLORS.error }]}>Check-In Blocked</Text>
                  </View>
                  <View style={styles.gpsLockView}>
                    <Ionicons name="close-circle" size={48} color={COLORS.error} style={{ marginBottom: 12 }} />
                    <Text style={[styles.gpsPlaceholderTitle, { color: COLORS.error }]}>Outside School Area</Text>
                    <Text style={styles.gpsPlaceholderDesc}>
                      Delhi Public School attendance check-ins are restricted to the school campus radius. You are currently out of range.
                    </Text>
                  </View>
                </View>
              )
            ) : (
              /* GPS Not Locked yet placeholder */
              <View style={[styles.hudCard, { opacity: 0.6 }]}>
                <View style={styles.hudHeader}>
                  <Ionicons name="lock-closed" size={20} color={COLORS.grayText} />
                  <Text style={styles.hudTitle}>Facial Photo Verification</Text>
                </View>
                <View style={styles.gpsLockView}>
                  <Text style={styles.gpsPlaceholderDesc}>
                    Facial selfie and attendance submissions are locked until a valid school boundary location signal is established.
                  </Text>
                </View>
              </View>
            )}
          </View>
        )}
      />
    );
  };

  // Render Check-In Timeline History View
  const renderHistoryTab = () => {
    const calendarDays = generateCalendarDays();
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const monthNames = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];

    // Filter logs for the selected calendar day
    let filteredLogs = historyLogs;
    if (selectedCalendarDay) {
      const selectedDateString = `${calendarYear}-${String(calendarMonth + 1).padStart(2, '0')}-${String(selectedCalendarDay).padStart(2, '0')}`;
      filteredLogs = historyLogs.filter(log => log.att_date && log.att_date.trim().substring(0, 10) === selectedDateString);
    }

    return (
      <FlatList
        data={filteredLogs}
        keyExtractor={(item, index) => String(index)}
        contentContainerStyle={styles.historyTimeline}
        showsVerticalScrollIndicator={false}
        ListHeaderComponent={() => (
          <View style={styles.calendarContainer}>
            {/* Summary Metrics */}
            <View style={styles.summaryStatsCard}>
              <Text style={styles.summaryCardTitle}>My Attendance Dashboard</Text>
              <View style={styles.summaryStatsRow}>
                <View style={[styles.statBox, { borderLeftColor: COLORS.success }]}>
                  <Text style={[styles.statNum, { color: COLORS.success }]}>
                    {calendarDays.filter(d => !d.spacer && d.status === 'PRESENT').length}
                  </Text>
                  <Text style={styles.statLabel}>Present</Text>
                </View>
                <View style={[styles.statBox, { borderLeftColor: COLORS.error }]}>
                  <Text style={[styles.statNum, { color: COLORS.error }]}>
                    {calendarDays.filter(d => !d.spacer && d.status === 'ABSENT').length}
                  </Text>
                  <Text style={styles.statLabel}>Absent</Text>
                </View>
                <View style={[styles.statBox, { borderLeftColor: COLORS.warning }]}>
                  <Text style={[styles.statNum, { color: COLORS.warning }]}>
                    {calendarDays.filter(d => !d.spacer && d.status === 'LEAVE').length}
                  </Text>
                  <Text style={styles.statLabel}>Leave</Text>
                </View>
              </View>
            </View>

            {/* Calendar Grid */}
            <View style={styles.calendarCard}>
              <View style={styles.calendarMonthHeader}>
                <TouchableOpacity onPress={() => changeMonth(-1)} style={styles.monthNavBtn}>
                  <Ionicons name="chevron-back" size={20} color={COLORS.primary} />
                </TouchableOpacity>
                <Text style={styles.calendarMonthTitle}>{monthNames[calendarMonth]} {calendarYear}</Text>
                <TouchableOpacity onPress={() => changeMonth(1)} style={styles.monthNavBtn}>
                  <Ionicons name="chevron-forward" size={20} color={COLORS.primary} />
                </TouchableOpacity>
              </View>

              <View style={styles.weekdayLabelsRow}>
                {weekdays.map(day => (
                  <Text style={styles.weekdayLabel} key={day}>{day}</Text>
                ))}
              </View>

              <View style={styles.daysGrid}>
                {calendarDays.map(item => renderDayCell(item))}
              </View>
            </View>

            {/* Logs List Header */}
            <View style={[styles.historyHeaderBlock, { marginTop: 14 }]}>
              <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
                <Text style={styles.historyHeaderTitle}>
                  {selectedCalendarDay
                    ? `Selfie Logs (${selectedCalendarDay} ${monthNames[calendarMonth].slice(0, 3)})`
                    : 'Recent Selfie Check-Ins'}
                </Text>
                {selectedCalendarDay ? (
                  <TouchableOpacity onPress={() => setSelectedCalendarDay(null)} activeOpacity={0.6}>
                    <Text style={{ fontSize: 12, color: COLORS.primary, fontWeight: 'bold' }}>Show All Logs</Text>
                  </TouchableOpacity>
                ) : null}
              </View>
              <Text style={styles.historyHeaderSub}>Showing logs of your past 30 days</Text>
            </View>
          </View>
        )}
        ListEmptyComponent={() => (
          <View style={styles.emptyHistoryCard}>
            <Ionicons name="calendar-outline" size={54} color="#CBD5E0" />
            <Text style={styles.emptyHistoryTitle}>No check-in logs found</Text>
            <Text style={styles.emptyHistoryDesc}>
              {selectedCalendarDay
                ? 'No check-in record found for the selected date.'
                : 'You have not registered any GPS selfie check-ins yet.'}
            </Text>
          </View>
        )}
        renderItem={({ item }) => (
          <View style={styles.timelineItem}>
            {/* Left timeline nodes */}
            <View style={styles.timelineNodeContainer}>
              <View style={styles.timelineNode} />
              <View style={styles.timelineLine} />
            </View>

            {/* Right log card */}
            <View style={styles.logCard}>
              <View style={styles.logCardHeader}>
                <Text style={styles.logDateText}>
                  {new Date(item.att_date).toLocaleDateString('en-US', {
                    weekday: 'short',
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                  })}
                </Text>
                <View style={item.checkout_time ? styles.completedBadge : styles.halfDayBadge}>
                  <Text style={item.checkout_time ? styles.completedBadgeText : styles.halfDayBadgeText}>
                    {item.checkout_time ? 'COMPLETED' : 'HALF DAY'}
                  </Text>
                </View>
              </View>

              <View style={styles.logGridContainer}>
                {/* Check-In Column */}
                <View style={styles.logPartColumn}>
                  <Text style={styles.logPartTitle}>CHECK-IN</Text>
                  <View style={styles.logPartRow}>
                    <TouchableOpacity
                      onPress={() => setPreviewPhotoUrl(`https://dpsmushkipur.com/bine/upload/${item.selfie_file}`)}
                      activeOpacity={0.8}
                    >
                      <Image
                        source={{ uri: `https://dpsmushkipur.com/bine/upload/${item.selfie_file}` }}
                        style={styles.logSelfieThumb}
                        defaultSource={require('./assets/default.png')}
                      />
                    </TouchableOpacity>
                    <View style={styles.logPartMeta}>
                      <Text style={styles.logTimeText}>
                        <Ionicons name="time-outline" size={10} color={COLORS.grayText} /> {formatTimeStr(item.created_at)}
                      </Text>
                      <TouchableOpacity
                        style={styles.mapsLinkBtnMini}
                        onPress={() => openMaps(item.latitude, item.longitude)}
                      >
                        <Ionicons name="map-outline" size={10} color={COLORS.primary} />
                        <Text style={styles.mapsLinkTextMini}>Maps</Text>
                      </TouchableOpacity>
                    </View>
                  </View>
                </View>

                {/* Vertical Divider */}
                <View style={styles.logGridDivider} />

                {/* Check-Out Column */}
                <View style={styles.logPartColumn}>
                  <Text style={styles.logPartTitle}>CHECK-OUT</Text>
                  {item.checkout_time ? (
                    <View style={styles.logPartRow}>
                      <TouchableOpacity
                        onPress={() => setPreviewPhotoUrl(`https://dpsmushkipur.com/bine/upload/${item.checkout_file}`)}
                        activeOpacity={0.8}
                      >
                        <Image
                          source={{ uri: `https://dpsmushkipur.com/bine/upload/${item.checkout_file}` }}
                          style={styles.logSelfieThumb}
                          defaultSource={require('./assets/default.png')}
                        />
                      </TouchableOpacity>
                      <View style={styles.logPartMeta}>
                        <Text style={styles.logTimeText}>
                          <Ionicons name="time-outline" size={10} color={COLORS.grayText} /> {formatTimeStr(item.checkout_time)}
                        </Text>
                        <TouchableOpacity
                          style={styles.mapsLinkBtnMini}
                          onPress={() => openMaps(item.checkout_latitude, item.checkout_longitude)}
                        >
                          <Ionicons name="map-outline" size={10} color={COLORS.primary} />
                          <Text style={styles.mapsLinkTextMini}>Maps</Text>
                        </TouchableOpacity>
                      </View>
                    </View>
                  ) : (
                    <View style={styles.logPartPendingContainer}>
                      <Ionicons name="alert-circle" size={14} color={COLORS.warning} />
                      <Text style={styles.logPendingText}>Pending</Text>
                    </View>
                  )}
                </View>
              </View>
            </View>
          </View>
        )}
      />
    );
  };

  if (isInitializing) {
    return (
      <SafeAreaProvider>
        <StatusBar style="light" />
        <LinearGradient colors={[COLORS.primary, '#0D5C14']} style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={COLORS.accent} />
          <Text style={styles.loadingText}>Initializing portal...</Text>
        </LinearGradient>
      </SafeAreaProvider>
    );
  }

  return (
    <SafeAreaProvider>
      <StatusBar style="light" />
      <SafeAreaView style={styles.container} edges={['top', 'bottom']}>
        {/* Header Block */}
        <View style={styles.header}>
          <LinearGradient colors={[COLORS.primary, '#0D5C14']} style={styles.headerGradient} />
          <View style={styles.headerContent}>
            <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
              <FontAwesome5 name="arrow-left" size={18} color="#fff" />
            </TouchableOpacity>
            <Text style={styles.headerTitle}>My Attendance</Text>
            <TouchableOpacity
              style={styles.infoButton}
              onPress={() => {
                Alert.alert(
                  'Facial GPS Attendance',
                  'This system uses dual-layered verification: real-time GPS location matching and camera facial authentication to secure check-ins.'
                );
              }}
            >
              <FontAwesome5 name="info-circle" size={18} color="#fff" />
            </TouchableOpacity>
          </View>
        </View>

        {/* Tab Buttons */}
        <View style={styles.tabsHeader}>
          <TouchableOpacity
            style={[styles.tabBtn, activeTab === 'checkin' && styles.activeTabBtn]}
            onPress={() => setActiveTab('checkin')}
          >
            <Ionicons name="navigate-outline" size={16} color={activeTab === 'checkin' ? '#fff' : COLORS.primary} style={{ marginRight: 6 }} />
            <Text style={[styles.tabBtnText, activeTab === 'checkin' && styles.activeTabBtnText]}>
              Secure Check-In
            </Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.tabBtn, activeTab === 'history' && styles.activeTabBtn]}
            onPress={() => {
              setActiveTab('history');
              fetchHistory(userId); // Refresh logs
            }}
          >
            <Ionicons name="time-outline" size={16} color={activeTab === 'history' ? '#fff' : COLORS.primary} style={{ marginRight: 6 }} />
            <Text style={[styles.tabBtnText, activeTab === 'history' && styles.activeTabBtnText]}>
              Monthly Logs
            </Text>
          </TouchableOpacity>
        </View>

        {/* Active Tab Screen */}
        <View style={{ flex: 1 }}>
          {activeTab === 'checkin' ? renderCheckInTab() : renderHistoryTab()}
        </View>

        {/* Premium Full Selfie Photo Popup Modal */}
        <Modal
          visible={!!previewPhotoUrl}
          transparent={true}
          animationType="fade"
          onRequestClose={() => setPreviewPhotoUrl(null)}
        >
          <View style={styles.modalBackdrop}>
            <View style={styles.modalContentCard}>
              <View style={styles.modalHeader}>
                <Text style={styles.modalTitleText}>Selfie Verification Photo</Text>
                <TouchableOpacity onPress={() => setPreviewPhotoUrl(null)} style={styles.closeModalBtn}>
                  <Ionicons name="close-circle" size={28} color="#999" />
                </TouchableOpacity>
              </View>

              {previewPhotoUrl ? (
                <Image
                  source={{ uri: previewPhotoUrl }}
                  style={styles.fullPreviewImage}
                  resizeMode="contain"
                />
              ) : null}

              <TouchableOpacity
                style={styles.dismissButton}
                onPress={() => setPreviewPhotoUrl(null)}
              >
                <Text style={styles.dismissBtnText}>Dismiss Preview</Text>
              </TouchableOpacity>
            </View>
          </View>
        </Modal>
      </SafeAreaView>
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F4F7F6',
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 16,
    fontSize: 16,
    color: '#fff',
    fontWeight: '600',
  },
  header: {
    height: 60,
    overflow: 'hidden',
  },
  headerGradient: {
    position: 'absolute',
    left: 0,
    right: 0,
    top: 0,
    bottom: 0,
  },
  headerContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    height: '100%',
  },
  backButton: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#fff',
  },
  infoButton: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255,255,255,0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  tabsHeader: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    padding: 8,
    marginHorizontal: 16,
    marginTop: 14,
    borderRadius: 14,
    gap: 8,
  },
  tabBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: 10,
    backgroundColor: '#F0FDF4',
  },
  activeTabBtn: {
    backgroundColor: '#1B5E20',
  },
  tabBtnText: {
    fontSize: 13,
    fontWeight: '700',
    color: '#1B5E20',
  },
  activeTabBtnText: {
    color: '#fff',
  },
  tabContentContainer: {
    paddingHorizontal: 16,
    paddingTop: 14,
    paddingBottom: 24,
  },
  workspace: {
    gap: 16,
  },
  hudCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 6,
    elevation: 2,
    borderWidth: 1,
    borderColor: '#EDF2F7',
  },
  hudHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#F7FAFC',
    paddingBottom: 10,
    marginBottom: 16,
    gap: 8,
  },
  hudTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  gpsLockView: {
    alignItems: 'center',
    paddingVertical: 14,
  },
  pulseContainer: {
    width: 64,
    height: 64,
    borderRadius: 32,
    borderWidth: 4,
    borderColor: '#E8F5E9',
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 14,
  },
  gpsPlaceholderTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: '#4A5568',
    marginBottom: 6,
  },
  gpsPlaceholderDesc: {
    fontSize: 12,
    color: '#718096',
    textAlign: 'center',
    lineHeight: 18,
    marginBottom: 16,
    paddingHorizontal: 12,
  },
  captureBtn: {
    borderRadius: 10,
    paddingHorizontal: 20,
    paddingVertical: 12,
    width: '80%',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  captureBtnText: {
    color: '#fff',
    fontSize: 13,
    fontWeight: 'bold',
  },
  gpsSuccessView: {
    alignItems: 'center',
    paddingVertical: 10,
  },
  pingSuccessRing: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#E8F5E9',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 12,
  },
  gpsCoordText: {
    fontSize: 15,
    fontWeight: 'bold',
    color: '#2D3748',
    marginBottom: 4,
  },
  gpsPrecisionText: {
    fontSize: 11,
    color: '#4CAF50',
    fontWeight: '600',
    marginBottom: 16,
  },
  recalBtn: {
    borderWidth: 1,
    borderColor: '#CBD5E0',
    borderRadius: 8,
    paddingHorizontal: 16,
    paddingVertical: 8,
  },
  recalBtnText: {
    color: '#4A5568',
    fontSize: 12,
    fontWeight: '600',
  },
  selfieSuccessView: {
    alignItems: 'center',
    paddingVertical: 6,
  },
  selfiePreviewContainer: {
    width: 100,
    height: 100,
    borderRadius: 50,
    overflow: 'hidden',
    borderWidth: 3,
    borderColor: '#B3E5FC',
    marginBottom: 12,
  },
  selfiePreview: {
    width: '100%',
    height: '100%',
  },
  selfieSuccessTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#2D3748',
    marginBottom: 12,
  },
  submitBigBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#FFC107',
    borderRadius: 14,
    paddingVertical: 15,
    marginTop: 8,
    shadowColor: '#FFC107',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 3,
  },
  disabledSubmitBtn: {
    backgroundColor: '#CBD5E0',
    shadowOpacity: 0,
    elevation: 0,
  },
  submitBigBtnText: {
    color: '#1B5E20',
    fontSize: 16,
    fontWeight: 'bold',
  },
  // Timeline History styles
  historyTimeline: {
    paddingHorizontal: 16,
    paddingTop: 16,
    paddingBottom: 24,
  },
  historyHeaderBlock: {
    marginBottom: 16,
  },
  historyHeaderTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  historyHeaderSub: {
    fontSize: 12,
    color: '#718096',
    marginTop: 2,
  },
  timelineItem: {
    flexDirection: 'row',
    marginBottom: 14,
  },
  timelineNodeContainer: {
    width: 24,
    alignItems: 'center',
  },
  timelineNode: {
    width: 12,
    height: 12,
    borderRadius: 6,
    backgroundColor: '#4CAF50',
    borderWidth: 2,
    borderColor: '#E8F5E9',
    marginTop: 16,
    zIndex: 2,
  },
  timelineLine: {
    position: 'absolute',
    top: 24,
    bottom: -24,
    width: 2,
    backgroundColor: '#E2E8F0',
  },
  logCard: {
    flex: 1,
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 12,
    marginLeft: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.02,
    shadowRadius: 4,
    elevation: 1,
    borderWidth: 1,
    borderColor: '#EDF2F7',
  },
  logCardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#F7FAFC',
    paddingBottom: 8,
    marginBottom: 8,
  },
  logDateText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  presentBadge: {
    backgroundColor: '#E8F5E9',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  presentBadgeText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: '#388E3C',
  },
  logBody: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  logSelfieThumb: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: '#EDF2F7',
    marginRight: 12,
  },
  logDetails: {
    flex: 1,
  },
  logCoordText: {
    fontSize: 11,
    color: '#718096',
    marginTop: 1,
  },
  mapsLinkBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 6,
  },
  mapsLinkText: {
    fontSize: 11,
    color: '#1B5E20',
    fontWeight: 'bold',
  },
  emptyHistoryCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 30,
    alignItems: 'center',
    marginTop: 40,
    borderWidth: 1,
    borderColor: '#EDF2F7',
  },
  emptyHistoryTitle: {
    fontSize: 15,
    fontWeight: 'bold',
    color: '#4A5568',
    marginTop: 14,
    marginBottom: 6,
  },
  emptyHistoryDesc: {
    fontSize: 12,
    color: '#718096',
    textAlign: 'center',
    lineHeight: 18,
    paddingHorizontal: 8,
  },
  // Checked-in / Already marked view styles
  checkedInRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 16,
    paddingVertical: 8,
  },
  checkedInSelfieContainer: {
    width: 110,
    height: 110,
    borderRadius: 14,
    overflow: 'hidden',
    borderWidth: 2,
    borderColor: '#E2E8F0',
    position: 'relative',
    backgroundColor: '#EDF2F7',
  },
  checkedInSelfie: {
    width: '100%',
    height: '100%',
  },
  viewSelfieOverlay: {
    position: 'absolute',
    bottom: 0,
    left: 0,
    right: 0,
    backgroundColor: 'rgba(0, 0, 0, 0.6)',
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 4,
    gap: 4,
  },
  viewSelfieText: {
    color: '#fff',
    fontSize: 10,
    fontWeight: 'bold',
  },
  checkedInMeta: {
    flex: 1,
    gap: 8,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  metaText: {
    fontSize: 13,
    color: '#4A5568',
    fontWeight: '500',
  },
  // Photo modal popup styles
  modalBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.65)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalContentCard: {
    width: width - 32,
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 16,
    maxHeight: height - 100,
    alignItems: 'center',
    gap: 16,
  },
  modalHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    width: '100%',
    borderBottomWidth: 1,
    borderBottomColor: '#F7FAFC',
    paddingBottom: 10,
  },
  modalTitleText: {
    fontSize: 15,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  closeModalBtn: {
    padding: 2,
  },
  fullPreviewImage: {
    width: '100%',
    height: width - 32,
    borderRadius: 12,
    backgroundColor: '#1A202C',
  },
  dismissButton: {
    backgroundColor: '#EDF2F7',
    borderRadius: 10,
    paddingVertical: 12,
    width: '100%',
    alignItems: 'center',
  },
  dismissBtnText: {
    color: '#4A5568',
    fontSize: 13,
    fontWeight: 'bold',
  },
  // Calendar Auditing Styles
  calendarContainer: {
    gap: 10,
    paddingBottom: 14,
  },
  summaryStatsCard: {
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 12,
    borderWidth: 1,
    borderColor: '#EDF2F7',
    gap: 8,
  },
  summaryCardTitle: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#4A5568',
  },
  summaryStatsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 8,
  },
  statBox: {
    flex: 1,
    backgroundColor: '#F7FAFC',
    padding: 8,
    borderRadius: 10,
    borderLeftWidth: 4,
    alignItems: 'center',
    gap: 1,
  },
  statNum: {
    fontSize: 14,
    fontWeight: 'bold',
  },
  statLabel: {
    fontSize: 10,
    color: '#718096',
    fontWeight: '600',
  },
  // Main Calendar Grid Styles
  calendarCard: {
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 10,
    borderWidth: 1,
    borderColor: '#EDF2F7',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.02,
    shadowRadius: 4,
    elevation: 2,
  },
  calendarMonthHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  monthNavBtn: {
    width: 28,
    height: 28,
    borderRadius: 14,
    backgroundColor: '#F0FDF4',
    alignItems: 'center',
    justifyContent: 'center',
  },
  calendarMonthTitle: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#1D2736',
  },
  weekdayLabelsRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 6,
    borderBottomWidth: 1,
    borderBottomColor: '#F7FAFC',
    paddingBottom: 4,
  },
  weekdayLabel: {
    width: '14.28%',
    textAlign: 'center',
    fontSize: 10,
    fontWeight: 'bold',
    color: '#A0AEC0',
  },
  daysGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    rowGap: 4,
  },
  calendarDayCell: {
    width: '14.28%',
    height: 38,
    justifyContent: 'center',
    alignItems: 'center',
  },
  dayCircle: {
    width: 26,
    height: 26,
    borderRadius: 13,
    justifyContent: 'center',
    alignItems: 'center',
  },
  calendarDayCellEmpty: {
    width: '14.28%',
    height: 38,
  },
  calendarDayNum: {
    fontSize: 12,
    fontWeight: 'bold',
  },
  statusDot: {
    width: 11,
    height: 11,
    borderRadius: 5.5,
    alignItems: 'center',
    justifyContent: 'center',
    position: 'absolute',
    bottom: 2,
    right: 2,
  },
  statusDotText: {
    fontSize: 7,
    fontWeight: 'bold',
    color: '#fff',
    lineHeight: 9,
  },
  logGridContainer: {
    flexDirection: 'row',
    alignItems: 'stretch',
    justifyContent: 'space-between',
    marginTop: 4,
  },
  logPartColumn: {
    flex: 1,
    paddingVertical: 4,
  },
  logPartTitle: {
    fontSize: 10,
    fontWeight: 'bold',
    color: '#718096',
    marginBottom: 6,
    letterSpacing: 0.5,
  },
  logPartRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  logPartMeta: {
    flex: 1,
    marginLeft: 8,
    gap: 4,
  },
  logTimeText: {
    fontSize: 11,
    fontWeight: '600',
    color: '#2D3748',
  },
  mapsLinkBtnMini: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 2,
    marginTop: 2,
  },
  mapsLinkTextMini: {
    fontSize: 10,
    color: '#1B5E20',
    fontWeight: 'bold',
  },
  logGridDivider: {
    width: 1,
    backgroundColor: '#E2E8F0',
    marginHorizontal: 8,
  },
  logPartPendingContainer: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
    backgroundColor: '#FFFDF5',
    borderRadius: 8,
    paddingVertical: 10,
    borderStyle: 'dashed',
    borderWidth: 1,
    borderColor: '#FFE082',
  },
  logPendingText: {
    fontSize: 11,
    fontWeight: 'bold',
    color: '#EF6C00',
  },
  completedBadge: {
    backgroundColor: '#E8F5E9',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  completedBadgeText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: '#388E3C',
  },
  halfDayBadge: {
    backgroundColor: '#FFF3E0',
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  halfDayBadgeText: {
    fontSize: 10,
    fontWeight: 'bold',
    color: '#EF6C00',
  },
});
