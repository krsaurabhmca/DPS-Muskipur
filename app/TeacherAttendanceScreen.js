import React, { useState, useEffect } from 'react';
import {
  StyleSheet,
  Text,
  View,
  TouchableOpacity,
  FlatList,
  TextInput,
  ActivityIndicator,
  Modal,
  Image,
  Dimensions,
  Alert,
  ScrollView,
  Platform
} from 'react-native';
import { Ionicons, FontAwesome5 } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import { SafeAreaProvider, SafeAreaView } from 'react-native-safe-area-context';

const { width, height } = Dimensions.get('window');

// Calculate distance using Haversine formula (returns meters)
const calculateDistance = (lat1, lon1, lat2, lon2) => {
  if (!lat1 || !lon1 || !lat2 || !lon2) return null;
  const R = 6371e3; // metres
  const φ1 = lat1 * Math.PI / 180;
  const φ2 = lat2 * Math.PI / 180;
  const Δφ = (lat2 - lat1) * Math.PI / 180;
  const Δλ = (lon2 - lon1) * Math.PI / 180;

  const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
    Math.cos(φ1) * Math.cos(φ2) *
    Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  return Math.round(R * c);
};

// Safe date parser for SQL datetime strings ("YYYY-MM-DD HH:MM:SS")
const parseSQLDate = (sqlDateStr) => {
  if (!sqlDateStr) return null;
  return new Date(sqlDateStr.replace(' ', 'T'));
};

export default function TeacherAttendanceScreen() {
  const router = useRouter();

  // Core state
  const [userRole, setUserRole] = useState(''); // 'ADMIN', 'DBA', 'TEACHER', 'STAFF'
  const [currentUserId, setCurrentUserId] = useState('');
  const [currentUserEmpId, setCurrentUserEmpId] = useState('');
  const [isLoading, setIsLoading] = useState(true);

  // Admin view state
  const [staffList, setStaffList] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]);

  // Selected teacher for calendar audit (used by Admin)
  const [auditTeacher, setAuditTeacher] = useState(null); 

  // Modal full photo state
  const [previewPhotoUrl, setPreviewPhotoUrl] = useState(null);

  // Calendar View state (used by Teachers, and Admins auditing a teacher)
  const [calendarMonth, setCalendarMonth] = useState(new Date().getMonth()); // 0-11
  const [calendarYear, setCalendarYear] = useState(new Date().getFullYear());
  const [attendanceLogs, setAttendanceLogs] = useState([]);
  const [attendanceSummary, setAttendanceSummary] = useState(null);
  const [loadingCalendar, setLoadingCalendar] = useState(false);
  const [selectedCalendarDay, setSelectedCalendarDay] = useState(new Date().getDate());
  const [schoolLocation, setSchoolLocation] = useState(null);

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
    warning: '#F57C00',
    blueAccent: '#00B0FF',
  };

  useEffect(() => {
    initializeScreen();
  }, []);

  const initializeScreen = async () => {
    try {
      setIsLoading(true);
      const role = await AsyncStorage.getItem('user_type'); // 'ADMIN', 'TEACHER', etc.
      const uId = await AsyncStorage.getItem('user_id');
      
      setUserRole(role || 'TEACHER');
      setCurrentUserId(uId || '');

      // Get employee association if teacher
      if (role === 'TEACHER' || role === 'STAFF') {
        const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=get_my_attendance', {
          user_id: uId
        });
        if (response.data && response.data.status === 'success') {
          // Store the logs & summary for current teacher
          setAttendanceLogs(response.data.selfie_logs || []);
          setAttendanceSummary(response.data.attendance || null);
          if (response.data.school_lat && response.data.school_lng) {
            setSchoolLocation({ lat: parseFloat(response.data.school_lat), lng: parseFloat(response.data.school_lng) });
          }
        }
      } else {
        // Logged in as Admin/DBA, fetch all teachers
        await fetchAllStaffAttendance(selectedDate);
      }
    } catch (error) {
      console.error('Error initializing screen:', error);
    } finally {
      setIsLoading(false);
    }
  };

  // Fetch all staff attendance (Admin option)
  const fetchAllStaffAttendance = async (date) => {
    try {
      setIsLoading(true);
      const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=get_all_employee_attendance', {
        att_date: date
      });
      if (response.data && response.data.status === 'success') {
        setStaffList(response.data.data || []);
      } else {
        Alert.alert('Error', 'Failed to fetch staff records.');
      }
    } catch (error) {
      console.error('Error fetching staff attendance:', error);
      Alert.alert('Network Error', 'Failed to connect to the server.');
    } finally {
      setIsLoading(false);
    }
  };

  // Fetch a specific employee's logs for calendar auditing (used by Admin)
  const fetchTeacherCalendarLogs = async (empId, teacherName) => {
    setLoadingCalendar(true);
    setAuditTeacher({ id: empId, name: teacherName });
    try {
      // Use get_emp_attendance which takes emp_id directly (employee.id)
      const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=get_emp_attendance', {
        emp_id: String(empId)
      });
      if (response.data && response.data.status === 'success') {
        setAttendanceLogs(response.data.selfie_logs || []);
        setAttendanceSummary(response.data.attendance || null);
        if (response.data.school_lat && response.data.school_lng) {
          setSchoolLocation({ lat: parseFloat(response.data.school_lat), lng: parseFloat(response.data.school_lng) });
        }
      }
    } catch (error) {
      console.error('Error loading calendar logs:', error);
    } finally {
      setLoadingCalendar(false);
    }
  };

  // Filtered staff list based on search query (Admin view)
  const getFilteredStaffList = () => {
    if (!searchQuery) return staffList;
    return staffList.filter(item => 
      item.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
      item.department.toLowerCase().includes(searchQuery.toLowerCase())
    );
  };

  // Switch month in Calendar view
  const changeMonth = (direction) => {
    let newMonth = calendarMonth + direction;
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
    
    // Automatically set selected day to today if navigating to current month/year, else null
    const today = new Date();
    if (newMonth === today.getMonth() && newYear === today.getFullYear()) {
      setSelectedCalendarDay(today.getDate());
    } else {
      setSelectedCalendarDay(null);
    }
  };

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
        const matchLog = attendanceLogs.find(log => log.att_date === dateString);
        
        if (matchLog) {
          status = 'PRESENT';
        } else if (attendanceSummary) {
          // 2. Check standard monthly attendance grid if summary exists
          // Summary has key matching current month, e.g. "Apr_2026", and keys d_1 to d_31
          const activeMonthObj = Array.isArray(attendanceSummary) ? attendanceSummary.find(
            item => item.att_month.replace(/\s+/g, '').toLowerCase() === currentMonthDBString.toLowerCase()
          ) : null;
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

  // Render Calendar View Screen (used by Teachers, and Admin Audits)
  const renderCalendarView = (teacherName = 'My') => {
    const monthNames = [
      'January', 'February', 'March', 'April', 'May', 'June',
      'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    const calendarDays = generateCalendarDays();
    const weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    // Filter check-in logs for the selected calendar day
    let filteredLogs = attendanceLogs;
    if (selectedCalendarDay) {
      const selectedDateString = `${calendarYear}-${String(calendarMonth + 1).padStart(2, '0')}-${String(selectedCalendarDay).padStart(2, '0')}`;
      filteredLogs = attendanceLogs.filter(log => log.att_date === selectedDateString);
    }

    return (
      <View style={styles.calendarContainer}>
        {/* Audit mode back button for Admin */}
        {userRole === 'ADMIN' || userRole === 'DBA' ? (
          <TouchableOpacity 
            style={styles.backToAuditsBtn} 
            onPress={() => setAuditTeacher(null)}
          >
            <Ionicons name="arrow-back" size={16} color={COLORS.primary} style={{ marginRight: 6 }} />
            <Text style={styles.backToAuditsText}>Back to Teacher List</Text>
          </TouchableOpacity>
        ) : null}

        {/* Card containing Calendar Summary metrics */}
        <View style={styles.summaryStatsCard}>
          <Text style={styles.summaryCardTitle}>{teacherName} Attendance Dashboard</Text>
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

        {/* Calendar Header with navigation switches */}
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

          {/* Weekday labels */}
          <View style={styles.weekdayLabelsRow}>
            {weekdays.map(day => (
              <Text style={styles.weekdayLabel} key={day}>{day}</Text>
            ))}
          </View>

          {/* Days Grid */}
          <View style={styles.daysGrid}>
            {calendarDays.map(item => renderDayCell(item))}
          </View>
        </View>

        {/* List of active location coordinate checkins for details below */}
        <View style={styles.coordinateLogsSection}>
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center', marginBottom: 4 }}>
              <Text style={styles.sectionHeaderTitle}>
                {selectedCalendarDay 
                  ? `Check-in Details (${selectedCalendarDay} ${monthNames[calendarMonth].slice(0, 3)})`
                  : 'Location Check-In History'}
              </Text>
              {selectedCalendarDay ? (
                <TouchableOpacity onPress={() => setSelectedCalendarDay(null)} activeOpacity={0.6}>
                  <Text style={{ fontSize: 11, color: COLORS.primary, fontWeight: 'bold' }}>Show All Logs</Text>
                </TouchableOpacity>
              ) : null}
            </View>

            {filteredLogs.length > 0 ? (
              filteredLogs.slice(0, 10).map((log, index) => {
                let distIn = null;
                if (log.latitude && schoolLocation) {
                  distIn = calculateDistance(parseFloat(log.latitude), parseFloat(log.longitude), schoolLocation.lat, schoolLocation.lng);
                }
                let distOut = null;
                if (log.checkout_latitude && schoolLocation) {
                  distOut = calculateDistance(parseFloat(log.checkout_latitude), parseFloat(log.checkout_longitude), schoolLocation.lat, schoolLocation.lng);
                }
                
                return (
                  <View style={styles.auditLogItem} key={index}>
                    <View style={{ flexDirection: 'column', gap: 6 }}>
                      {/* Check-In Photo */}
                      {log.selfie_file ? (
                        <TouchableOpacity
                          onPress={() => setPreviewPhotoUrl(`https://dpsmushkipur.com/bine/upload/${log.selfie_file}`)}
                        >
                          <Image
                            source={{ uri: `https://dpsmushkipur.com/bine/upload/${log.selfie_file}` }}
                            style={styles.auditLogThumb}
                          />
                        </TouchableOpacity>
                      ) : (
                        <View style={[styles.auditLogThumb, { backgroundColor: '#EEF2FF', justifyContent: 'center', alignItems: 'center' }]}>
                          <Ionicons name="camera-off-outline" size={22} color="#CBD5E0" />
                        </View>
                      )}
                      
                      {/* Check-Out Photo */}
                      {log.checkout_file ? (
                        <TouchableOpacity
                          onPress={() => setPreviewPhotoUrl(`https://dpsmushkipur.com/bine/upload/${log.checkout_file}`)}
                        >
                          <Image
                            source={{ uri: `https://dpsmushkipur.com/bine/upload/${log.checkout_file}` }}
                            style={styles.auditLogThumb}
                          />
                        </TouchableOpacity>
                      ) : null}
                    </View>
                    
                    <View style={styles.auditLogContent}>
                      <Text style={styles.auditLogDate}>
                        {new Date(log.att_date).toLocaleDateString('en-IN', {
                          weekday: 'short', month: 'short', day: 'numeric', year: 'numeric'
                        })}
                      </Text>
                      
                      {log.created_at ? (
                        <View style={{ marginTop: 4 }}>
                          {/* IN Info */}
                          <Text style={{ fontSize: 12, color: COLORS.success, fontWeight: '600' }}>
                            ✓ IN: {parseSQLDate(log.created_at)?.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true })}
                            {distIn !== null ? `  (${distIn}m away)` : ''}
                          </Text>
                          
                          {/* OUT Info */}
                          <Text style={{ fontSize: 12, color: log.checkout_time ? COLORS.error : COLORS.grayText, fontWeight: '600', marginTop: 2 }}>
                            ✗ OUT: {log.checkout_time ? parseSQLDate(log.checkout_time)?.toLocaleTimeString('en-IN', { hour: '2-digit', minute: '2-digit', hour12: true }) : 'Not yet'}
                            {distOut !== null ? `  (${distOut}m away)` : ''}
                          </Text>
                        </View>
                      ) : null}
                    </View>
                  </View>
                );
              })
            ) : (
              <View style={styles.noLogsContainer}>
                <Ionicons name="calendar-outline" size={32} color="#CBD5E0" style={{ marginBottom: 6 }} />
                <Text style={styles.noLogsText}>No attendance records logged for this date.</Text>
              </View>
            )}
          </View>
      </View>
    );
  };

  // Render Admin View: List of all Teachers
  const renderAdminListView = () => {
    const filteredStaff = getFilteredStaffList();

    const ListHeader = () => (
      <View style={styles.adminHeaderHUD}>
        <View style={styles.searchBarContainer}>
          <Ionicons name="search" size={18} color={COLORS.grayText} style={{ marginRight: 8 }} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search by name or department..."
            placeholderTextColor={COLORS.grayText}
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
          {searchQuery ? (
            <TouchableOpacity onPress={() => setSearchQuery('')}>
              <Ionicons name="close-circle" size={18} color={COLORS.grayText} />
            </TouchableOpacity>
          ) : null}
        </View>

        <View style={styles.dateSelectorRow}>
          <Ionicons name="calendar" size={18} color={COLORS.primary} style={{ marginRight: 8 }} />
          <Text style={styles.dateLabelText}>
            Reporting Date: {new Date(selectedDate).toLocaleDateString('en-IN', {
              day: 'numeric', month: 'short', year: 'numeric'
            })}
          </Text>
        </View>
      </View>
    );

    return (
      <FlatList
        data={filteredStaff}
        keyExtractor={(item) => String(item.emp_id)}
        showsVerticalScrollIndicator={false}
        style={{ flex: 1 }}
        contentContainerStyle={{ padding: 14, gap: 10, paddingBottom: 30 }}
        ListHeaderComponent={<ListHeader />}
        ListHeaderComponentStyle={{ marginBottom: 14 }}
        ListEmptyComponent={() => (
          <View style={styles.emptyStateContainer}>
            <Ionicons name="people-outline" size={54} color="#CBD5E0" />
            <Text style={styles.emptyStateTitle}>No records found</Text>
            <Text style={styles.emptyStateDesc}>Could not find any teacher check-in status matching query.</Text>
          </View>
        )}
        renderItem={({ item }) => {
          let statusColor = COLORS.error;
          let statusText = 'ABSENT';
          if (item.status === 'PRESENT') {
            statusColor = COLORS.success;
            statusText = `PRESENT (${item.checkin_info?.checkin_time || 'Check-in'})`;
          } else if (item.status === 'LEAVE') {
            statusColor = COLORS.warning;
            statusText = 'LEAVE';
          }

          return (
            <TouchableOpacity
              style={styles.teacherCard}
              activeOpacity={0.9}
              onPress={() => fetchTeacherCalendarLogs(item.emp_id, item.name)}
            >
              <View style={styles.teacherCardContent}>
                {/* Photo Thumbnail */}
                {item.checkin_info?.selfie_file ? (
                  <TouchableOpacity
                    activeOpacity={0.8}
                    onPress={() => setPreviewPhotoUrl(`https://dpsmushkipur.com/bine/upload/${item.checkin_info.selfie_file}`)}
                  >
                    <Image
                      source={{ uri: `https://dpsmushkipur.com/bine/upload/${item.checkin_info.selfie_file}` }}
                      style={styles.teacherPhotoThumb}
                    />
                    <View style={styles.zoomIconPill}>
                      <Ionicons name="expand" size={10} color="#fff" />
                    </View>
                  </TouchableOpacity>
                ) : (
                  <View style={styles.teacherPhotoPlaceholder}>
                    <Ionicons name="person" size={24} color="#CBD5E0" />
                  </View>
                )}

                {/* Instructor Details */}
                <View style={styles.teacherDetails}>
                  <Text style={styles.teacherNameText}>{item.name}</Text>
                  <Text style={styles.teacherDeptText}>{item.designation} • {item.department}</Text>
                  <View style={styles.statusPillRow}>
                    <View style={[styles.statusIndicatorDot, { backgroundColor: statusColor }]} />
                    <Text style={[styles.statusTextValue, { color: statusColor }]}>{statusText}</Text>
                  </View>
                </View>

                <Ionicons name="chevron-forward" size={18} color="#CBD5E0" />
              </View>

              {/* Location coordinates snippet if PRESENT */}
              {item.checkin_info?.latitude ? (
                <View style={styles.cardCoordsFooter}>
                  <Ionicons name="navigate-outline" size={12} color={COLORS.grayText} style={{ marginRight: 4 }} />
                  <Text style={styles.coordsFooterText} numberOfLines={1}>
                    Logged GPS: {parseFloat(item.checkin_info.latitude).toFixed(5)}, {parseFloat(item.checkin_info.longitude).toFixed(5)}
                  </Text>
                </View>
              ) : null}
            </TouchableOpacity>
          );
        }}
      />
    );
  };


  return (
    <SafeAreaProvider>
      <StatusBar style="light" />
      <SafeAreaView style={styles.container} edges={['top', 'bottom']}>
        
        {/* Header HUD Layout */}
        <View style={styles.header}>
          <LinearGradient colors={[COLORS.primary, '#0D5C14']} style={styles.headerGradient} />
          <View style={styles.headerContent}>
            <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
              <FontAwesome5 name="arrow-left" size={18} color="#fff" />
            </TouchableOpacity>
            <Text style={styles.headerTitle}>
              {auditTeacher ? `${auditTeacher.name}'s Logs` : userRole === 'ADMIN' || userRole === 'DBA' ? 'Staff Reports' : 'My Reports'}
            </Text>
            <View style={{ width: 36 }} />
          </View>
        </View>

        {/* Loading Spinner */}
        {isLoading ? (
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color={COLORS.primary} />
            <Text style={styles.loadingText}>Fetching attendance records...</Text>
          </View>
        ) : (
          auditTeacher ? (
            loadingCalendar ? (
              <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color={COLORS.primary} />
                <Text style={styles.loadingText}>Loading calendar logs...</Text>
              </View>
            ) : (
              <ScrollView
                style={{ flex: 1 }}
                contentContainerStyle={styles.scrollContent}
                showsVerticalScrollIndicator={false}
              >
                {renderCalendarView(auditTeacher.name)}
              </ScrollView>
            )
          ) : (userRole === 'ADMIN' || userRole === 'DBA') ? (
            /* FlatList manages its own scroll — no outer ScrollView */
            renderAdminListView()
          ) : (
            <ScrollView
              style={{ flex: 1 }}
              contentContainerStyle={styles.scrollContent}
              showsVerticalScrollIndicator={false}
            >
              {renderCalendarView('My')}
            </ScrollView>
          )
        )}


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
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 50,
  },
  loadingText: {
    marginTop: 16,
    fontSize: 14,
    color: '#4A5568',
    fontWeight: '600',
  },
  scrollContent: {
    paddingHorizontal: 16,
    paddingTop: 16,
  },

  // Admin list view styles
  adminContainer: {
    gap: 14,
  },
  adminHeaderHUD: {
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 12,
    borderWidth: 1,
    borderColor: '#EDF2F7',
    gap: 10,
  },
  searchBarContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F7FAFC',
    borderRadius: 10,
    paddingHorizontal: 12,
    height: 40,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  searchInput: {
    flex: 1,
    fontSize: 13,
    color: '#2D3748',
    padding: 0,
  },
  dateSelectorRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 4,
  },
  dateLabelText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#4A5568',
  },
  teacherCard: {
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 12,
    borderWidth: 1,
    borderColor: '#EDF2F7',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.02,
    shadowRadius: 4,
    elevation: 1,
    marginBottom: 12,
  },
  teacherCardContent: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  teacherPhotoThumb: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#F7FAFC',
    borderWidth: 2,
    borderColor: '#E2E8F0',
  },
  zoomIconPill: {
    position: 'absolute',
    bottom: -2,
    right: -2,
    backgroundColor: 'rgba(0,0,0,0.6)',
    width: 18,
    height: 18,
    borderRadius: 9,
    alignItems: 'center',
    justifyContent: 'center',
  },
  teacherPhotoPlaceholder: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: '#EDF2F7',
    alignItems: 'center',
    justifyContent: 'center',
  },
  teacherDetails: {
    flex: 1,
    marginLeft: 14,
    gap: 4,
  },
  teacherNameText: {
    fontSize: 15,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  teacherDeptText: {
    fontSize: 12,
    color: '#718096',
  },
  statusPillRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  statusIndicatorDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  statusTextValue: {
    fontSize: 11,
    fontWeight: 'bold',
  },
  cardCoordsFooter: {
    flexDirection: 'row',
    alignItems: 'center',
    borderTopWidth: 1,
    borderTopColor: '#F7FAFC',
    paddingTop: 8,
    marginTop: 8,
  },
  coordsFooterText: {
    fontSize: 11,
    color: '#718096',
  },

  // Calendar Auditing Styles
  calendarContainer: {
    gap: 10,
    paddingBottom: 24,
  },
  backToAuditsBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#E8F5E9',
    paddingVertical: 10,
    paddingHorizontal: 16,
    borderRadius: 10,
    alignSelf: 'flex-start',
  },
  backToAuditsText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#1B5E20',
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

  // Coordinate checkins logs details
  coordinateLogsSection: {
    gap: 10,
    marginTop: 10,
  },
  sectionHeaderTitle: {
    fontSize: 15,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  auditLogItem: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 10,
    borderWidth: 1,
    borderColor: '#EDF2F7',
  },
  auditLogThumb: {
    width: 44,
    height: 44,
    borderRadius: 8,
    backgroundColor: '#F7FAFC',
  },
  auditLogContent: {
    flex: 1,
    marginLeft: 12,
    gap: 2,
  },
  auditLogDate: {
    fontSize: 13,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  auditLogCoordinates: {
    fontSize: 11,
    color: '#718096',
  },
  viewThumbBtn: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#F0FDF4',
    alignItems: 'center',
    justifyContent: 'center',
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
  emptyStateContainer: {
    alignItems: 'center',
    paddingVertical: 60,
    gap: 8,
  },
  emptyStateTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#4A5568',
    marginTop: 6,
  },
  emptyStateDesc: {
    fontSize: 12,
    color: '#718096',
    textAlign: 'center',
    paddingHorizontal: 30,
  },
  noLogsContainer: {
    backgroundColor: '#fff',
    borderRadius: 12,
    padding: 20,
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: '#EDF2F7',
    borderStyle: 'dashed',
    marginTop: 6,
  },
  noLogsText: {
    fontSize: 12,
    color: '#718096',
    fontWeight: '600',
    textAlign: 'center',
  },
});
