import { FontAwesome5, Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Animated,
  FlatList,
  Linking, // Add this import
  Modal,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View,
} from 'react-native';
import {
  SafeAreaProvider,
  useSafeAreaInsets
} from 'react-native-safe-area-context';

const ExamReportScreen = () => {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  
  const [examList, setExamList] = useState([]);
  const [selectedExam, setSelectedExam] = useState('');
  const [studentId, setStudentId] = useState('');
  const [reportData, setReportData] = useState(null);
  const [loading, setLoading] = useState(false);
  const [examListLoading, setExamListLoading] = useState(true);
  const [error, setError] = useState(null);
  const [examModalVisible, setExamModalVisible] = useState(false);
  
  // Animation values
  const [fadeAnim] = useState(new Animated.Value(0));
  const [slideAnim] = useState(new Animated.Value(50));

  // DPS Colors
  const COLORS = {
    primary: '#2E7D32', // DPS Green
    secondary: '#1B5E20', // Dark Green
    yellow: '#FBC02D', // DPS Yellow
    lightYellow: '#FFF9C4',
    white: '#FFFFFF',
    lightGray: '#F5F5F5',
    darkGray: '#333333',
    error: '#D32F2F',
    success: '#388E3C',
    border: '#E8F5E9',
    lightGreen: '#F1F8E9',
  };

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
      })
    ]).start();
  }, []);

  useEffect(() => {
    initializeScreen();
  }, []);

  const initializeScreen = async () => {
    await getStudentId();
    await fetchExamList();
  };

  const getStudentId = async () => {
    try {
      const id = await AsyncStorage.getItem('student_id');
      if (id) {
        setStudentId(id);
      } else {
        Alert.alert('Error', 'Student ID not found. Please login again.');
      }
    } catch (error) {
      console.error('Error getting student ID:', error);
      Alert.alert('Error', 'Failed to retrieve student information.');
    }
  };

  const fetchExamList = async () => {
    setExamListLoading(true);
    setError(null);

    try {
      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=get_exam'
      );

      if (!response.ok) {
        throw new Error('Failed to fetch exam list');
      }

      const data = await response.json();

      if (Array.isArray(data) && data.length > 0) {
        setExamList(data);
        setSelectedExam(data[0]);
        // Auto-fetch first exam report
        await fetchMarksReport(data[0]);
      } else {
        throw new Error('No exams found');
      }
    } catch (error) {
      console.error('Error fetching exam list:', error);
      setError('Failed to load exam list. Please try again.');
      Alert.alert('Error', 'Failed to load exam list. Please check your connection.');
    } finally {
      setExamListLoading(false);
    }
  };

  const fetchMarksReport = async (examName) => {
    if (!studentId || !examName) {
      Alert.alert('Error', 'Please select an exam');
      return;
    }

    setLoading(true);
    setError(null);
    setReportData(null);

    try {
      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=get_marks',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            student_id: studentId,
            exam_name: examName,
          }),
        }
      );

      if (!response.ok) {
        throw new Error('Failed to fetch marks');
      }

      const data = await response.json();

      if (data.status === 'success') {
        setReportData(data);
      } else {
        throw new Error(data.message || 'Failed to fetch marks');
      }
    } catch (error) {
      console.error('Error fetching marks:', error);
      setError('Failed to load report. Please try again.');
      Alert.alert('Error', 'Failed to load exam report. Please check your connection.');
    } finally {
      setLoading(false);
    }
  };

  const handleExamChange = (examName) => {
    setSelectedExam(examName);
    setExamModalVisible(false);
    fetchMarksReport(examName);
  };

  // Function to open PDF Marksheet
  const handleOpenMarksheet = async () => {
    if (!studentId || !selectedExam) {
      Alert.alert('Error', 'Please select an exam first');
      return;
    }

    try {
      const url = `https://dpsmushkipur.com/bine/exam_report_pdf?student_id=${studentId}&exam_name=${encodeURIComponent(selectedExam)}`;
      
      const supported = await Linking.canOpenURL(url);
      
      if (supported) {
        await Linking.openURL(url);
      } else {
        Alert.alert('Error', 'Unable to open marksheet');
      }
    } catch (error) {
      console.error('Error opening marksheet:', error);
      Alert.alert('Error', 'Failed to open marksheet. Please try again.');
    }
  };

  // Calculate percentage and grade
  const calculatePercentage = () => {
    if (!reportData || !reportData.subjects) return 0;
    const maxMarks = reportData.subjects.length * 50;
    return ((reportData.grand_total / maxMarks) * 100).toFixed(2);
  };

  const getGrade = (percentage) => {
    if (percentage >= 90) return { grade: 'A1', color: '#2E7D32' };
    if (percentage >= 80) return { grade: 'A2', color: '#388E3C' };
    if (percentage >= 70) return { grade: 'B1', color: '#689F38' };
    if (percentage >= 60) return { grade: 'B2', color: '#FBC02D' };
    if (percentage >= 50) return { grade: 'C', color: '#FF9800' };
    return { grade: 'D', color: '#D32F2F' };
  };

  const getGradeColor = (grade) => {
    switch (grade) {
      case 'A+': return '#2E7D32';
      case 'A': return '#388E3C';
      case 'B+': return '#689F38';
      case 'B': return '#8BC34A';
      case 'C': return '#FBC02D';
      case 'D': return '#FF9800';
      case 'N/A': return '#9E9E9E';
      default: return '#757575';
    }
  };

  // Render unified header with exam selector
  const renderHeaderWithExamSelector = () => (
    <View style={styles.unifiedHeader}>
      <LinearGradient
        colors={[COLORS.primary, COLORS.secondary]}
        style={styles.unifiedHeaderGradient}
      >
        {/* Header Navigation */}
        <View style={styles.headerContent}>
          <TouchableOpacity
            style={styles.backButton}
            onPress={() => router.back()}
            activeOpacity={0.7}
          >
            <FontAwesome5 name="arrow-left" size={20} color="#ffffff" />
          </TouchableOpacity>
          <Text style={styles.headerTitle}>Exam Report</Text>
          <TouchableOpacity
            style={styles.infoButton}
            onPress={() => {
              Alert.alert(
                "Exam Report Information",
                "This screen shows your exam performance with detailed subject-wise marks breakdown and co-scholastic activities.",
                [{ text: "OK" }]
              );
            }}
            activeOpacity={0.7}
          >
            <FontAwesome5 name="info-circle" size={20} color="#ffffff" />
          </TouchableOpacity>
        </View>

        {/* Exam Selector Inside Header */}
        <View style={styles.examSelectorContent}>
          <View style={styles.examSelectorHeader}>
            <Ionicons name="document-text" size={22} color={COLORS.yellow} />
            <Text style={styles.examSelectorTitle}>Select Exam</Text>
          </View>
          <TouchableOpacity
            style={styles.examSelector}
            onPress={() => setExamModalVisible(true)}
            activeOpacity={0.8}
          >
            <Ionicons
              name="clipboard-outline"
              size={20}
              color={COLORS.primary}
              style={styles.selectorIcon}
            />
            <Text style={styles.examSelectorText}>
              {selectedExam || 'Select exam'}
            </Text>
            <Ionicons name="chevron-down" size={20} color={COLORS.primary} />
          </TouchableOpacity>
        </View>
      </LinearGradient>
    </View>
  );

  // Exam Selection Modal
  const ExamSelectionModal = () => (
    <Modal
      visible={examModalVisible}
      transparent={true}
      animationType="slide"
      statusBarTranslucent={true}
      onRequestClose={() => setExamModalVisible(false)}
    >
      <View style={[styles.modalOverlay, { paddingTop: insets.top }]}>
        <Pressable
          style={{ flex: 1 }}
          onPress={() => setExamModalVisible(false)}
        />
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <View style={styles.modalHandle} />
            <Text style={styles.modalTitle}>Select Exam</Text>
            <FlatList
              data={examList}
              keyExtractor={(item, index) => index.toString()}
              renderItem={({ item }) => (
                <Pressable
                  style={({ pressed }) => [
                    styles.modalItem,
                    pressed && styles.modalItemPressed,
                    selectedExam === item && styles.selectedModalItem
                  ]}
                  onPress={() => handleExamChange(item)}
                  android_ripple={{ color: 'rgba(46, 125, 50, 0.1)' }}
                >
                  <View style={styles.examIconContainer}>
                    <Ionicons name="document-text" size={20} color="#fff" />
                  </View>
                  <View style={styles.modalItemContent}>
                    <Text style={styles.modalItemText}>{item}</Text>
                  </View>
                  {selectedExam === item && (
                    <Ionicons name="checkmark-circle" size={24} color={COLORS.primary} />
                  )}
                </Pressable>
              )}
              ItemSeparatorComponent={() => <View style={styles.modalSeparator} />}
              contentContainerStyle={styles.modalList}
            />
            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setExamModalVisible(false)}
              activeOpacity={0.7}
            >
              <Text style={styles.modalCloseButtonText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );

  // Render student info card
  const renderStudentInfo = () => {
    if (!reportData) return null;

    const percentage = calculatePercentage();
    const gradeInfo = getGrade(percentage);

    return (
      <Animated.View 
        style={[
          styles.studentCard,
          {
            opacity: fadeAnim,
            transform: [{ translateY: slideAnim }]
          }
        ]}
      >
        <LinearGradient
          colors={[COLORS.primary, COLORS.secondary]}
          style={styles.studentCardGradient}
          start={{ x: 0, y: 0 }}
          end={{ x: 1, y: 1 }}
        >
          <View style={styles.studentHeader}>
            <View style={styles.studentIconContainer}>
              <Ionicons name="person" size={32} color={COLORS.white} />
            </View>
            <View style={styles.studentDetails}>
              <Text style={styles.studentName}>{reportData.student.name}</Text>
              <View style={styles.studentMetaRow}>
                <View style={styles.metaItem}>
                  <Ionicons name="school-outline" size={14} color="rgba(255,255,255,0.9)" />
                  <Text style={styles.metaValue}>Class {reportData.student.class}</Text>
                </View>
                <View style={styles.metaItem}>
                  <Ionicons name="card-outline" size={14} color="rgba(255,255,255,0.9)" />
                  <Text style={styles.metaValue}>ID: {reportData.student.id}</Text>
                </View>
              </View>
            </View>
          </View>

          <View style={styles.performanceRow}>
            <View style={styles.performanceItem}>
              <Text style={styles.performanceLabel}>Total Marks</Text>
              <Text style={styles.performanceValue}>{reportData.grand_total}</Text>
            </View>
            <View style={styles.performanceDivider} />
            <View style={styles.performanceItem}>
              <Text style={styles.performanceLabel}>Percentage</Text>
              <Text style={styles.performanceValue}>{percentage}%</Text>
            </View>
            <View style={styles.performanceDivider} />
            <View style={styles.performanceItem}>
              <Text style={styles.performanceLabel}>Grade</Text>
              <View style={[styles.gradeBadge, { backgroundColor: COLORS.yellow }]}>
                <Text style={[styles.gradeText, { color: COLORS.primary }]}>
                  {gradeInfo.grade}
                </Text>
              </View>
            </View>
          </View>
        </LinearGradient>
      </Animated.View>
    );
  };

  // Render Marksheet Button
  const renderMarksheetButton = () => {
    if (!reportData || !studentId || !selectedExam) return null;

    return (
      <Animated.View 
        style={[
          styles.marksheetButtonContainer,
          {
            opacity: fadeAnim,
            transform: [{ translateY: slideAnim }]
          }
        ]}
      >
        <TouchableOpacity
          style={styles.marksheetButton}
          onPress={handleOpenMarksheet}
          activeOpacity={0.8}
        >
          <LinearGradient
            colors={[COLORS.yellow, '#F9A825']}
            style={styles.marksheetButtonGradient}
            start={{ x: 0, y: 0 }}
            end={{ x: 1, y: 0 }}
          >
            <Ionicons name="document-text" size={24} color={COLORS.primary} />
            <Text style={styles.marksheetButtonText}>Download Marksheet</Text>
            <Ionicons name="download-outline" size={20} color={COLORS.primary} />
          </LinearGradient>
        </TouchableOpacity>
      </Animated.View>
    );
  };

  // Render scholastic marks table
  const renderScholasticTable = () => {
    if (!reportData || !reportData.subjects) return null;

    return (
      <Animated.View 
        style={[
          styles.reportCard,
          {
            opacity: fadeAnim,
            transform: [{ translateY: slideAnim }]
          }
        ]}
      >
        <View style={styles.reportHeader}>
          <Ionicons name="book" size={24} color={COLORS.primary} />
          <Text style={styles.reportTitle}>Scholastic Areas</Text>
        </View>

        <View style={styles.tableContainer}>
          {/* Table Header */}
          <View style={styles.tableHeader}>
            <Text style={[styles.tableHeaderText, styles.subjectColumn]}>Subject</Text>
            <Text style={[styles.tableHeaderText, styles.markColumn]}>NB</Text>
            <Text style={[styles.tableHeaderText, styles.markColumn]}>SE</Text>
            <Text style={[styles.tableHeaderText, styles.markColumn]}>MO</Text>
            <Text style={[styles.tableHeaderText, styles.totalColumn]}>Total</Text>
          </View>

          {/* Table Rows */}
          {reportData.subjects.map((subject, index) => (
            <View
              key={subject.subject_id}
              style={[
                styles.tableRow,
                index % 2 === 0 ? styles.evenRow : styles.oddRow,
              ]}
            >
              <View style={styles.subjectColumnContainer}>
                <View style={[styles.subjectIcon, { backgroundColor: getSubjectColor(index) }]}>
                  <Ionicons name="book" size={12} color="#fff" />
                </View>
                <Text style={styles.subjectName}>{subject.subject_name}</Text>
              </View>
              <Text style={[styles.tableCell, styles.markColumn]}>{subject.marks.nb}</Text>
              <Text style={[styles.tableCell, styles.markColumn]}>{subject.marks.se}</Text>
              <Text style={[styles.tableCell, styles.markColumn]}>{subject.marks.mo}</Text>
              <Text style={[styles.tableCell, styles.totalColumn, styles.totalMark]}>
                {subject.marks.total}
              </Text>
            </View>
          ))}

          {/* Grand Total Row */}
          <LinearGradient
            colors={[COLORS.primary, COLORS.secondary]}
            style={styles.grandTotalRow}
            start={{ x: 0, y: 0 }}
            end={{ x: 1, y: 0 }}
          >
            <View style={styles.grandTotalContent}>
              <Ionicons name="trophy" size={20} color={COLORS.yellow} />
              <Text style={styles.grandTotalLabel}>Grand Total</Text>
            </View>
            <Text style={styles.grandTotalValue}>{reportData.grand_total}</Text>
          </LinearGradient>
        </View>
      </Animated.View>
    );
  };

  // Render co-scholastic activities
  const renderCoScholastic = () => {
    if (!reportData || !reportData.co_scholastic || reportData.co_scholastic.length === 0) return null;

    return (
      <Animated.View 
        style={[
          styles.reportCard,
          {
            opacity: fadeAnim,
            transform: [{ translateY: slideAnim }]
          }
        ]}
      >
        <View style={styles.reportHeader}>
          <Ionicons name="medal" size={24} color={COLORS.yellow} />
          <Text style={styles.reportTitle}>Co-Scholastic Areas</Text>
        </View>

        <View style={styles.coScholasticContainer}>
          {reportData.co_scholastic.map((activity, index) => (
            <View
              key={index}
              style={[
                styles.coScholasticRow,
                index % 2 === 0 ? styles.evenRow : styles.oddRow,
              ]}
            >
              <View style={styles.coScholasticContent}>
                <View style={[styles.activityIcon, { backgroundColor: getActivityColor(index) }]}>
                  <Ionicons name={getActivityIcon(activity.area)} size={16} color="#fff" />
                </View>
                <Text style={styles.activityName}>{activity.area}</Text>
              </View>
              <View style={[styles.gradeChip, { backgroundColor: getGradeColor(activity.grade) }]}>
                <Text style={styles.gradeChipText}>{activity.grade}</Text>
              </View>
            </View>
          ))}
        </View>

        <View style={styles.gradeInfoBox}>
          <Ionicons name="information-circle-outline" size={18} color={COLORS.primary} />
          <Text style={styles.gradeInfoText}>
            Grading Scale: A1 (Outstanding), A2 (Excellent), B1 (Very Good), B2 (Good), C (Fair), D (Needs Improvement)
          </Text>
        </View>
      </Animated.View>
    );
  };

  const getSubjectColor = (index) => {
    const colors = ['#2E7D32', '#388E3C', '#43A047', '#66BB6A', '#81C784', '#A5D6A7'];
    return colors[index % colors.length];
  };

  const getActivityColor = (index) => {
    const colors = ['#FBC02D', '#F9A825', '#F57F17'];
    return colors[index % colors.length];
  };

  const getActivityIcon = (area) => {
    if (area.toLowerCase().includes('work')) return 'construct-outline';
    if (area.toLowerCase().includes('health') || area.toLowerCase().includes('physical')) return 'fitness-outline';
    if (area.toLowerCase().includes('discipline')) return 'star-outline';
    if (area.toLowerCase().includes('art')) return 'color-palette-outline';
    if (area.toLowerCase().includes('music')) return 'musical-notes-outline';
    return 'ribbon-outline';
  };

  // Render error state
  const renderError = () => (
    <View style={styles.centerContainer}>
      <View style={styles.errorIconContainer}>
        <Ionicons name="alert-circle" size={64} color={COLORS.error} />
      </View>
      <Text style={styles.errorText}>{error}</Text>
      <TouchableOpacity
        style={styles.retryButton}
        onPress={() => fetchMarksReport(selectedExam)}
        activeOpacity={0.8}
      >
        <Ionicons name="refresh" size={20} color={COLORS.white} />
        <Text style={styles.retryButtonText}>Retry</Text>
      </TouchableOpacity>
    </View>
  );

  // Render empty state
  const renderEmptyState = () => (
    <View style={styles.centerContainer}>
      <View style={styles.emptyIconContainer}>
        <Ionicons name="document-text-outline" size={64} color="#999" />
      </View>
      <Text style={styles.emptyText}>Select an exam to view report</Text>
    </View>
  );

  // Render loading state
  const renderLoading = () => (
    <View style={styles.centerContainer}>
      <ActivityIndicator size="large" color={COLORS.primary} />
      <Text style={styles.loadingText}>Loading report...</Text>
    </View>
  );

  if (examListLoading) {
    return (
      <SafeAreaProvider>
        <StatusBar style="light" backgroundColor={COLORS.primary} />
        <View style={styles.safeArea}>
          {renderHeaderWithExamSelector()}
          <View style={styles.centerContainer}>
            <ActivityIndicator size="large" color={COLORS.primary} />
            <Text style={styles.loadingText}>Loading exams...</Text>
          </View>
        </View>
      </SafeAreaProvider>
    );
  }

  return (
    <SafeAreaProvider>
      <StatusBar style="light" backgroundColor={COLORS.primary} />
      <View style={styles.safeArea}>
        {renderHeaderWithExamSelector()}

        <ScrollView
          style={styles.container}
          contentContainerStyle={[
            styles.contentContainer,
            { paddingBottom: 20 + insets.bottom }
          ]}
          showsVerticalScrollIndicator={false}
        >
          {loading ? (
            renderLoading()
          ) : error && !reportData ? (
            renderError()
          ) : reportData ? (
            <>
              {renderStudentInfo()}
              {renderMarksheetButton()}
              {renderScholasticTable()}
              {renderCoScholastic()}
            </>
          ) : (
            renderEmptyState()
          )}
        </ScrollView>

        <ExamSelectionModal />
      </View>
    </SafeAreaProvider>
  );
};

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  
  // Unified Header with Exam Selector
  unifiedHeader: {
    overflow: 'hidden',
  },
  unifiedHeaderGradient: {
    paddingTop: 50,
    paddingHorizontal: 20,
    paddingBottom: 25,
    borderBottomLeftRadius: 25,
    borderBottomRightRadius: 25,
  },
  headerContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
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
    color: '#ffffff',
  },
  infoButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  
  // Exam Selector Inside Header
  examSelectorContent: {
    gap: 10,
  },
  examSelectorHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  examSelectorTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#FFFFFF',
  },
  examSelector: {
    backgroundColor: '#FFFFFF',
    borderRadius: 12,
    padding: 15,
    flexDirection: 'row',
    alignItems: 'center',
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.15,
    shadowRadius: 3,
  },
  selectorIcon: {
    marginRight: 10,
  },
  examSelectorText: {
    fontSize: 15,
    color: '#333',
    flex: 1,
    fontWeight: '500',
  },
  
  container: {
    flex: 1,
  },
  contentContainer: {
    padding: 15,
  },
  studentCard: {
    borderRadius: 12,
    marginBottom: 20,
    overflow: 'hidden',
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.1,
    shadowRadius: 6,
  },
  studentCardGradient: {
    padding: 20,
  },
  studentHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 20,
  },
  studentIconContainer: {
    width: 56,
    height: 56,
    borderRadius: 28,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  studentDetails: {
    flex: 1,
  },
  studentName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#FFFFFF',
    marginBottom: 8,
  },
  studentMetaRow: {
    flexDirection: 'row',
    gap: 15,
  },
  metaItem: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  metaValue: {
    fontSize: 13,
    color: 'rgba(255, 255, 255, 0.9)',
    fontWeight: '500',
  },
  performanceRow: {
    flexDirection: 'row',
    backgroundColor: 'rgba(255, 255, 255, 0.15)',
    borderRadius: 10,
    padding: 15,
    justifyContent: 'space-around',
  },
  performanceItem: {
    alignItems: 'center',
    flex: 1,
  },
  performanceLabel: {
    fontSize: 12,
    color: 'rgba(255, 255, 255, 0.8)',
    marginBottom: 5,
  },
  performanceValue: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  gradeBadge: {
    paddingHorizontal: 12,
    paddingVertical: 4,
    borderRadius: 12,
  },
  gradeText: {
    fontSize: 18,
    fontWeight: 'bold',
  },
  performanceDivider: {
    width: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
  },

  // Marksheet Button
  marksheetButtonContainer: {
    marginBottom: 20,
  },
  marksheetButton: {
    borderRadius: 12,
    overflow: 'hidden',
    elevation: 4,
    shadowColor: '#FBC02D',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 5,
  },
  marksheetButtonGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 16,
    paddingHorizontal: 20,
    gap: 10,
  },
  marksheetButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2E7D32',
    flex: 1,
    textAlign: 'center',
  },

  reportCard: {
    backgroundColor: 'white',
    borderRadius: 12,
    padding: 15,
    marginBottom: 20,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
  },
  reportHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 15,
    paddingBottom: 12,
    borderBottomWidth: 2,
    borderBottomColor: '#FBC02D',
  },
  reportTitle: {
    fontSize: 16,
    fontWeight: '600',
    color: '#2E7D32',
    marginLeft: 8,
    flex: 1,
  },
  
  // Scholastic Table
  tableContainer: {
    borderRadius: 8,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#E8F5E9',
  },
  tableHeader: {
    flexDirection: 'row',
    backgroundColor: '#2E7D32',
    paddingVertical: 12,
    paddingHorizontal: 10,
  },
  tableHeaderText: {
    color: '#FFFFFF',
    fontWeight: 'bold',
    fontSize: 13,
    textAlign: 'center',
  },
  tableRow: {
    flexDirection: 'row',
    paddingVertical: 12,
    paddingHorizontal: 10,
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#E8F5E9',
  },
  evenRow: {
    backgroundColor: '#F1F8E9',
  },
  oddRow: {
    backgroundColor: '#FFFFFF',
  },
  subjectColumn: {
    flex: 2,
    textAlign: 'left',
  },
  subjectColumnContainer: {
    flex: 2,
    flexDirection: 'row',
    alignItems: 'center',
  },
  subjectIcon: {
    width: 20,
    height: 20,
    borderRadius: 10,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 8,
  },
  subjectName: {
    fontSize: 14,
    color: '#333',
    fontWeight: '600',
    flex: 1,
  },
  markColumn: {
    flex: 1,
    textAlign: 'center',
  },
  totalColumn: {
    flex: 1,
    textAlign: 'center',
  },
  tableCell: {
    fontSize: 14,
    color: '#666',
  },
  totalMark: {
    fontWeight: 'bold',
    color: '#2E7D32',
    fontSize: 15,
  },
  grandTotalRow: {
    paddingVertical: 14,
    paddingHorizontal: 15,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  grandTotalContent: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
  },
  grandTotalLabel: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
  grandTotalValue: {
    fontSize: 22,
    fontWeight: 'bold',
    color: '#FBC02D',
  },

  // Co-Scholastic
  coScholasticContainer: {
    borderRadius: 8,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: '#FFF9C4',
  },
  coScholasticRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 14,
    paddingHorizontal: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#FFF9C4',
  },
  coScholasticContent: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  activityIcon: {
    width: 32,
    height: 32,
    borderRadius: 16,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  activityName: {
    fontSize: 14,
    color: '#333',
    fontWeight: '600',
    flex: 1,
  },
  gradeChip: {
    paddingHorizontal: 16,
    paddingVertical: 6,
    borderRadius: 16,
    minWidth: 50,
    alignItems: 'center',
  },
  gradeChipText: {
    color: '#FFFFFF',
    fontSize: 13,
    fontWeight: 'bold',
  },
  gradeInfoBox: {
    flexDirection: 'row',
    backgroundColor: '#F1F8E9',
    padding: 12,
    borderRadius: 8,
    marginTop: 12,
    gap: 8,
  },
  gradeInfoText: {
    fontSize: 11,
    color: '#666',
    flex: 1,
    lineHeight: 16,
  },

  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 60,
  },
  errorIconContainer: {
    marginBottom: 10,
  },
  emptyIconContainer: {
    marginBottom: 10,
  },
  loadingText: {
    marginTop: 16,
    fontSize: 15,
    color: '#666',
  },
  errorText: {
    marginTop: 16,
    fontSize: 15,
    color: '#D32F2F',
    textAlign: 'center',
    paddingHorizontal: 32,
  },
  emptyText: {
    marginTop: 16,
    fontSize: 15,
    color: '#999',
    textAlign: 'center',
  },
  retryButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#2E7D32',
    paddingHorizontal: 24,
    paddingVertical: 12,
    borderRadius: 10,
    marginTop: 20,
    elevation: 2,
    shadowColor: '#2E7D32',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 3,
  },
  retryButtonText: {
    color: '#FFFFFF',
    fontSize: 15,
    fontWeight: '600',
    marginLeft: 8,
  },

  // Modal styles
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
  modalContainer: {
    justifyContent: 'flex-end',
    height: '60%',
  },
  modalContent: {
    backgroundColor: 'white',
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    padding: 20,
    height: '100%',
  },
  modalHandle: {
    width: 40,
    height: 5,
    backgroundColor: '#E0E0E0',
    borderRadius: 3,
    alignSelf: 'center',
    marginBottom: 20,
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#333',
    marginBottom: 15,
    textAlign: 'center',
  },
  modalList: {
    paddingVertical: 10,
  },
  modalItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 14,
    paddingHorizontal: 10,
    borderRadius: 10,
  },
  modalItemPressed: {
    backgroundColor: 'rgba(46, 125, 50, 0.08)',
  },
  selectedModalItem: {
    backgroundColor: '#F1F8E9',
  },
  examIconContainer: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: '#2E7D32',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  modalItemContent: {
    flex: 1,
  },
  modalItemText: {
    fontSize: 15,
    color: '#333',
    fontWeight: '500',
  },
  modalSeparator: {
    height: 1,
    backgroundColor: '#F0F0F0',
    marginVertical: 2,
  },
  modalCloseButton: {
    marginTop: 15,
    padding: 14,
    backgroundColor: '#F5F5F5',
    borderRadius: 10,
    alignItems: 'center',
  },
  modalCloseButtonText: {
    fontSize: 16,
    color: '#555',
    fontWeight: '500',
  },
});

export default ExamReportScreen;