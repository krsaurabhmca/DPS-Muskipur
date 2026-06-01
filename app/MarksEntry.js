import { FontAwesome5, Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  FlatList,
  KeyboardAvoidingView,
  Modal,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View
} from 'react-native';
import { SafeAreaProvider, SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';

export default function MarksEntryScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();

  // Primary state variables
  const [teacherId, setTeacherId] = useState('');
  const [assignments, setAssignments] = useState([]);
  const [selectedAssignment, setSelectedAssignment] = useState(null);
  const [exams, setExams] = useState([]);
  const [selectedExam, setSelectedExam] = useState('');
  const [students, setStudents] = useState([]);
  const [marks, setMarks] = useState({}); // Stores marks dynamically by student_id: { nb, se, mo }
  
  // UI and loading states
  const [isLoading, setIsLoading] = useState(true);
  const [isSheetLoading, setIsSheetLoading] = useState(false);
  const [isSaving, setIsSaving] = useState(false);
  const [assignmentModalVisible, setAssignmentModalVisible] = useState(false);
  const [examModalVisible, setExamModalVisible] = useState(false);
  const [sheetLoaded, setSheetLoaded] = useState(false);

  // DPS Theme Colors
  const COLORS = {
    primary: '#1B5E20',     // Premium Green
    secondary: '#4CAF50',   // Vibrant Green
    accent: '#FFC107',      // Yellow/Amber
    lightGreen: '#E8F5E9',
    white: '#FFFFFF',
    darkText: '#2D3748',
    lightGray: '#F7FAFC',
    border: '#E2E8F0',
    grayText: '#718096',
    success: '#388E3C',
    error: '#D32F2F',
  };

  useEffect(() => {
    initializeScreen();
  }, []);

  const initializeScreen = async () => {
    try {
      setIsLoading(true);
      const uId = await AsyncStorage.getItem('user_id');
      if (!uId) {
        Alert.alert('Session Expired', 'Please login again to continue.', [
          { text: 'OK', onPress: () => router.replace('/admin_login') }
        ]);
        return;
      }
      setTeacherId(uId);
      await Promise.all([fetchAssignments(uId), fetchExams()]);
    } catch (error) {
      console.error('Error initializing Marks Entry:', error);
      Alert.alert('Error', 'Failed to initialize screen. Please try again.');
    } finally {
      setIsLoading(false);
    }
  };

  // Fetch teacher class/subject assignments
  const fetchAssignments = async (uId) => {
    try {
      const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=get_teacher_assignments', {
        user_id: uId
      });
      if (response.data && Array.isArray(response.data.data)) {
        setAssignments(response.data.data);
      } else if (response.data && response.data.status === 'error') {
        Alert.alert('Info', response.data.message || 'No active teaching assignments found.');
      }
    } catch (error) {
      console.log('--- DETAILED ASSIGNMENTS FETCH ERROR ---');
      console.log('Message:', error.message);
      if (error.response) {
        console.log('Status:', error.response.status);
        console.log('Data:', error.response.data);
      } else if (error.request) {
        console.log('Request sent but no response received (Network Timeout / Offline)');
      }
      console.log('URL tried:', error.config?.url);
      console.log('----------------------------------------');
    }
  };

  // Fetch exam list
  const fetchExams = async () => {
    try {
      const response = await axios.get('https://dpsmushkipur.com/bine/api.php?task=get_exam');
      if (Array.isArray(response.data)) {
        setExams(response.data);
        if (response.data.length > 0) {
          setSelectedExam(response.data[0]); // Auto-select first exam
        }
      }
    } catch (error) {
      console.error('Error fetching exams:', error);
    }
  };

  // Load student list with pre-existing marks
  const loadMarksSheet = async () => {
    if (!selectedAssignment) {
      Alert.alert('Required', 'Please select class, section, and subject.');
      return;
    }
    if (!selectedExam) {
      Alert.alert('Required', 'Please select an exam type.');
      return;
    }

    setIsSheetLoading(true);
    setSheetLoaded(false);
    
    try {
      const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=get_class_marks', {
        student_class: selectedAssignment.student_class,
        student_section: selectedAssignment.student_section,
        exam_name: selectedExam,
        subject: selectedAssignment.subject_column
      });

      if (response.data && response.data.status === 'success' && Array.isArray(response.data.data)) {
        const studentData = response.data.data;
        setStudents(studentData);

        // Pre-fill existing marks in local state
        const initialMarks = {};
        studentData.forEach(student => {
          initialMarks[student.id] = {
            nb: student.marks?.nb !== undefined ? String(student.marks.nb) : '',
            se: student.marks?.se !== undefined ? String(student.marks.se) : '',
            mo: student.marks?.mo !== undefined ? String(student.marks.mo) : ''
          };
        });
        setMarks(initialMarks);
        setSheetLoaded(true);
      } else {
        Alert.alert('Marks Sheet', 'Failed to retrieve students. Please verify backend connection.');
      }
    } catch (error) {
      console.error('Error loading marks sheet:', error);
      Alert.alert('Network Error', 'Failed to load students. Please check your internet connection.');
    } finally {
      setIsSheetLoading(false);
    }
  };

  // Update dynamic input state
  const handleMarkChange = (studentId, field, value) => {
    // Basic sanitization to allow decimal inputs and limit value ranges
    if (value !== '' && !/^\d*\.?\d*$/.test(value)) return;
    
    setMarks(prev => ({
      ...prev,
      [studentId]: {
        ...prev[studentId],
        [field]: value
      }
    }));
  };

  // Submit all student marks to backend
  const saveMarksSheet = async () => {
    setIsSaving(true);
    try {
      const marksData = students.map(student => {
        const studMarks = marks[student.id];
        return {
          student_id: student.id,
          student_admission: student.student_admission,
          nb: studMarks.nb !== '' ? parseFloat(studMarks.nb) : 0,
          se: studMarks.se !== '' ? parseFloat(studMarks.se) : 0,
          mo: studMarks.mo !== '' ? parseFloat(studMarks.mo) : 0
        };
      });

      const response = await axios.post('https://dpsmushkipur.com/bine/api.php?task=marks_entry', {
        exam_name: selectedExam,
        subject: selectedAssignment.subject_column,
        marks_data: marksData
      });

      if (response.data && response.data.status === 'success') {
        Alert.alert('Marks Saved', `Successfully updated marks for the class!\n\nSubject: ${selectedAssignment.subject_name}\nExam: ${selectedExam}`, [
          { text: 'OK' }
        ]);
      } else {
        Alert.alert('Failed', response.data.message || 'Error occurred while saving marks.');
      }
    } catch (error) {
      console.error('Error saving marks:', error);
      Alert.alert('Error', 'Network error. Failed to save marks sheet.');
    } finally {
      setIsSaving(false);
    }
  };

  // Render modal item selector
  const AssignmentModal = () => (
    <Modal
      visible={assignmentModalVisible}
      transparent={true}
      animationType="slide"
      statusBarTranslucent={true}
      onRequestClose={() => setAssignmentModalVisible(false)}
    >
      <View style={[styles.modalOverlay, { paddingTop: insets.top }]}>
        <Pressable style={{ flex: 1 }} onPress={() => setAssignmentModalVisible(false)} />
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <View style={styles.modalHandle} />
            <Text style={styles.modalTitle}>Choose Assigned Subject</Text>
            {assignments.length > 0 ? (
              <FlatList
                data={assignments}
                keyExtractor={(item) => String(item.id)}
                renderItem={({ item }) => (
                  <Pressable
                    style={({ pressed }) => [
                      styles.modalItem,
                      pressed && styles.modalItemPressed,
                      selectedAssignment?.id === item.id && styles.selectedModalItem
                    ]}
                    onPress={() => {
                      setSelectedAssignment(item);
                      setAssignmentModalVisible(false);
                      setSheetLoaded(false); // Reset sheet on selection change
                    }}
                  >
                    <View style={[styles.modalIconContainer, { backgroundColor: COLORS.primary }]}>
                      <Ionicons name="school" size={18} color="#fff" />
                    </View>
                    <View style={styles.modalItemContent}>
                      <Text style={styles.modalItemText}>
                        Class {item.student_class} - Section {item.student_section}
                      </Text>
                      <Text style={styles.modalItemSubtext}>{item.subject_name}</Text>
                    </View>
                    {selectedAssignment?.id === item.id && (
                      <Ionicons name="checkmark-circle" size={24} color={COLORS.primary} />
                    )}
                  </Pressable>
                )}
                ItemSeparatorComponent={() => <View style={styles.modalSeparator} />}
              />
            ) : (
              <View style={styles.emptyContainer}>
                <Ionicons name="alert-circle-outline" size={48} color={COLORS.grayText} />
                <Text style={styles.emptyText}>No assigned classes found for this account.</Text>
              </View>
            )}
            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setAssignmentModalVisible(false)}
            >
              <Text style={styles.modalCloseButtonText}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );

  const ExamModal = () => (
    <Modal
      visible={examModalVisible}
      transparent={true}
      animationType="slide"
      statusBarTranslucent={true}
      onRequestClose={() => setExamModalVisible(false)}
    >
      <View style={[styles.modalOverlay, { paddingTop: insets.top }]}>
        <Pressable style={{ flex: 1 }} onPress={() => setExamModalVisible(false)} />
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <View style={styles.modalHandle} />
            <Text style={styles.modalTitle}>Choose Exam Term</Text>
            <FlatList
              data={exams}
              keyExtractor={(item, index) => String(index)}
              renderItem={({ item }) => (
                <Pressable
                  style={({ pressed }) => [
                    styles.modalItem,
                    pressed && styles.modalItemPressed,
                    selectedExam === item && styles.selectedModalItem
                  ]}
                  onPress={() => {
                    setSelectedExam(item);
                    setExamModalVisible(false);
                    setSheetLoaded(false);
                  }}
                >
                  <View style={[styles.modalIconContainer, { backgroundColor: COLORS.accent }]}>
                    <Ionicons name="document-text" size={18} color="#fff" />
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
            />
            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setExamModalVisible(false)}
            >
              <Text style={styles.modalCloseButtonText}>Close</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );

  // Render individual student marks card
  const renderStudentRow = ({ item }) => {
    const studMarks = marks[item.id] || { nb: '', se: '', mo: '' };
    return (
      <View style={styles.studentCard}>
        {/* Name and Roll Section */}
        <View style={styles.studentDetails}>
          <View style={styles.rollBadge}>
            <Text style={styles.rollNumber}>{item.student_roll}</Text>
          </View>
          <View style={styles.nameSection}>
            <Text style={styles.studentName} numberOfLines={1}>{item.student_name}</Text>
            <Text style={styles.studentAdmission}>Admission: {item.student_admission}</Text>
          </View>
        </View>

        {/* Input Fields Row */}
        <View style={styles.marksInputRow}>
          {/* Note Book */}
          <View style={styles.inputWrapper}>
            <Text style={styles.inputLabelText}>NB (5)</Text>
            <TextInput
              style={styles.markInput}
              keyboardType="decimal-pad"
              maxLength={4}
              placeholder="0.0"
              value={studMarks.nb}
              onChangeText={(val) => handleMarkChange(item.id, 'nb', val)}
            />
          </View>

          {/* Subject Enrichment */}
          <View style={styles.inputWrapper}>
            <Text style={styles.inputLabelText}>SE (5)</Text>
            <TextInput
              style={styles.markInput}
              keyboardType="decimal-pad"
              maxLength={4}
              placeholder="0.0"
              value={studMarks.se}
              onChangeText={(val) => handleMarkChange(item.id, 'se', val)}
            />
          </View>

          {/* Marks Obtained */}
          <View style={styles.inputWrapper}>
            <Text style={[styles.inputLabelText, { color: COLORS.primary, fontWeight: '700' }]}>MO (80)</Text>
            <TextInput
              style={[styles.markInput, styles.moInput]}
              keyboardType="decimal-pad"
              maxLength={5}
              placeholder="0.0"
              value={studMarks.mo}
              onChangeText={(val) => handleMarkChange(item.id, 'mo', val)}
            />
          </View>
        </View>
      </View>
    );
  };

  if (isLoading) {
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
        {/* Header Section */}
        <View style={styles.header}>
          <LinearGradient colors={[COLORS.primary, '#0D5C14']} style={styles.headerGradient} />
          <View style={styles.headerContent}>
            <TouchableOpacity style={styles.backButton} onPress={() => router.back()}>
              <FontAwesome5 name="arrow-left" size={18} color="#fff" />
            </TouchableOpacity>
            <Text style={styles.headerTitle}>Marks Entry</Text>
            <TouchableOpacity
              style={styles.infoButton}
              onPress={() => {
                Alert.alert(
                  'Instructions',
                  '1. Select your assigned class & subject.\n2. Choose the active exam term.\n3. Input Notebook (NB, max 5), Subject Enrichment (SE, max 5) and Marks Obtained (MO, max 80) for each student.\n4. Click Save Marks when done.'
                );
              }}
            >
              <FontAwesome5 name="info-circle" size={18} color="#fff" />
            </TouchableOpacity>
          </View>
        </View>

        {/* Selection Bar */}
        <View style={styles.selectionCard}>
          <Text style={styles.cardSectionTitle}>Configure Sheet</Text>
          <View style={styles.selectionRow}>
            {/* Subject Selector */}
            <TouchableOpacity
              style={styles.selectorBtn}
              onPress={() => setAssignmentModalVisible(true)}
            >
              <Ionicons name="book-outline" size={18} color={COLORS.primary} style={{ marginRight: 6 }} />
              <Text style={styles.selectorBtnText} numberOfLines={1}>
                {selectedAssignment
                  ? `${selectedAssignment.student_class}-${selectedAssignment.student_section} (${selectedAssignment.subject_name})`
                  : 'Select Subject'}
              </Text>
              <Ionicons name="chevron-down" size={14} color={COLORS.grayText} />
            </TouchableOpacity>

            {/* Exam Term Selector */}
            <TouchableOpacity
              style={styles.selectorBtn}
              onPress={() => setExamModalVisible(true)}
            >
              <Ionicons name="document-text-outline" size={18} color={COLORS.primary} style={{ marginRight: 6 }} />
              <Text style={styles.selectorBtnText} numberOfLines={1}>
                {selectedExam || 'Select Exam'}
              </Text>
              <Ionicons name="chevron-down" size={14} color={COLORS.grayText} />
            </TouchableOpacity>
          </View>

          {/* Load Button */}
          <TouchableOpacity
            style={[styles.loadButton, isSheetLoading && { opacity: 0.8 }]}
            onPress={loadMarksSheet}
            disabled={isSheetLoading}
          >
            {isSheetLoading ? (
              <ActivityIndicator size="small" color="#fff" />
            ) : (
              <>
                <Text style={styles.loadButtonText}>Load Marks Sheet</Text>
                <Ionicons name="search" size={16} color="#fff" style={{ marginLeft: 8 }} />
              </>
            )}
          </TouchableOpacity>
        </View>

        {/* Marks Sheet Workspace */}
        {isSheetLoading ? (
          <View style={styles.loadingSheetContainer}>
            <ActivityIndicator size="large" color={COLORS.primary} />
            <Text style={styles.loadingSheetText}>Loading class records...</Text>
          </View>
        ) : sheetLoaded ? (
          <KeyboardAvoidingView
            behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
            style={{ flex: 1 }}
            keyboardVerticalOffset={Platform.OS === 'ios' ? 80 : 0}
          >
            <View style={styles.sheetHeaderInfo}>
              <View style={styles.sheetTitleCol}>
                <Text style={styles.sheetTitle}>Student Marks Sheet</Text>
                <Text style={styles.sheetSub}>{students.length} Students Active</Text>
              </View>
              <View style={styles.subjectPill}>
                <Text style={styles.subjectPillText}>{selectedAssignment?.subject_name}</Text>
              </View>
            </View>

            <FlatList
              data={students}
              keyExtractor={(item) => String(item.id)}
              renderItem={renderStudentRow}
              contentContainerStyle={styles.listContainer}
              showsVerticalScrollIndicator={true}
            />

            {/* Save Action Footer */}
            <View style={styles.footerContainer}>
              <TouchableOpacity
                style={[styles.saveButton, isSaving && { opacity: 0.8 }]}
                onPress={saveMarksSheet}
                disabled={isSaving}
              >
                {isSaving ? (
                  <ActivityIndicator size="small" color="#fff" />
                ) : (
                  <>
                    <Ionicons name="save-outline" size={20} color="#fff" style={{ marginRight: 8 }} />
                    <Text style={styles.saveButtonText}>Save & Commit Marks</Text>
                  </>
                )}
              </TouchableOpacity>
            </View>
          </KeyboardAvoidingView>
        ) : (
          <ScrollView contentContainerStyle={styles.emptySheetContainer}>
            <View style={styles.emptyStateCard}>
              <Ionicons name="clipboard-outline" size={72} color="#CBD5E0" />
              <Text style={styles.emptyStateTitle}>Marks Sheet Empty</Text>
              <Text style={styles.emptyStateDesc}>
                Configure your subject assignment and active exam term above, then click 'Load Marks Sheet' to load the student registry.
              </Text>
            </View>
          </ScrollView>
        )}

        {/* Modal Panels */}
        <AssignmentModal />
        <ExamModal />
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
  selectionCard: {
    backgroundColor: '#fff',
    margin: 14,
    padding: 14,
    borderRadius: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 3,
  },
  cardSectionTitle: {
    fontSize: 13,
    fontWeight: '700',
    color: '#718096',
    textTransform: 'uppercase',
    letterSpacing: 0.5,
    marginBottom: 10,
  },
  selectionRow: {
    flexDirection: 'row',
    gap: 10,
    marginBottom: 12,
  },
  selectorBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#F7FAFC',
    borderWidth: 1,
    borderColor: '#E2E8F0',
    borderRadius: 10,
    paddingHorizontal: 12,
    paddingVertical: 12,
  },
  selectorBtnText: {
    flex: 1,
    fontSize: 13,
    fontWeight: '600',
    color: '#2D3748',
    marginRight: 4,
  },
  loadButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#1B5E20',
    borderRadius: 10,
    paddingVertical: 12,
    shadowColor: '#1B5E20',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.2,
    shadowRadius: 6,
    elevation: 3,
  },
  loadButtonText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: '700',
  },
  loadingSheetContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingSheetText: {
    marginTop: 12,
    fontSize: 14,
    color: '#718096',
    fontWeight: '500',
  },
  sheetHeaderInfo: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    marginBottom: 10,
  },
  sheetTitleCol: {
    flex: 1,
  },
  sheetTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  sheetSub: {
    fontSize: 12,
    color: '#718096',
    marginTop: 2,
  },
  subjectPill: {
    backgroundColor: '#E8F5E9',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 15,
  },
  subjectPillText: {
    color: '#1B5E20',
    fontSize: 12,
    fontWeight: '700',
  },
  listContainer: {
    paddingHorizontal: 14,
    paddingBottom: 24,
  },
  studentCard: {
    backgroundColor: '#fff',
    borderRadius: 14,
    padding: 12,
    marginBottom: 12,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.03,
    shadowRadius: 4,
    elevation: 1,
    borderWidth: 1,
    borderColor: '#EDF2F7',
  },
  studentDetails: {
    flexDirection: 'row',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#F7FAFC',
    paddingBottom: 10,
    marginBottom: 10,
  },
  rollBadge: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#E8F5E9',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 10,
  },
  rollNumber: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#1B5E20',
  },
  nameSection: {
    flex: 1,
  },
  studentName: {
    fontSize: 14,
    fontWeight: '700',
    color: '#2D3748',
  },
  studentAdmission: {
    fontSize: 11,
    color: '#A0AEC0',
    marginTop: 1,
  },
  marksInputRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 12,
  },
  inputWrapper: {
    flex: 1,
    alignItems: 'center',
  },
  inputLabelText: {
    fontSize: 11,
    color: '#718096',
    fontWeight: '600',
    marginBottom: 6,
  },
  markInput: {
    width: '100%',
    height: 42,
    backgroundColor: '#F7FAFC',
    borderWidth: 1,
    borderColor: '#E2E8F0',
    borderRadius: 8,
    textAlign: 'center',
    fontSize: 14,
    fontWeight: '600',
    color: '#2D3748',
  },
  moInput: {
    borderColor: '#C8E6C9',
    backgroundColor: '#E8F5E9',
    color: '#1B5E20',
  },
  footerContainer: {
    backgroundColor: '#fff',
    padding: 14,
    borderTopWidth: 1,
    borderTopColor: '#E2E8F0',
  },
  saveButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#FFC107',
    borderRadius: 12,
    paddingVertical: 14,
    shadowColor: '#FFC107',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 4,
  },
  saveButtonText: {
    color: '#1B5E20',
    fontSize: 15,
    fontWeight: 'bold',
  },
  emptySheetContainer: {
    flexGrow: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
  },
  emptyStateCard: {
    backgroundColor: '#fff',
    borderRadius: 20,
    padding: 30,
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.05,
    shadowRadius: 10,
    elevation: 2,
    width: '100%',
  },
  emptyStateTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#4A5568',
    marginTop: 16,
    marginBottom: 8,
  },
  emptyStateDesc: {
    fontSize: 13,
    color: '#718096',
    textAlign: 'center',
    lineHeight: 20,
  },
  // Modal Styles
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  modalContainer: {
    backgroundColor: '#fff',
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    maxHeight: '80%',
  },
  modalContent: {
    padding: 20,
  },
  modalHandle: {
    width: 40,
    height: 4,
    borderRadius: 2,
    backgroundColor: '#E2E8F0',
    alignSelf: 'center',
    marginBottom: 16,
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2D3748',
    marginBottom: 16,
    textAlign: 'center',
  },
  modalItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 14,
    paddingHorizontal: 10,
    borderRadius: 12,
  },
  modalItemPressed: {
    backgroundColor: '#F7FAFC',
  },
  selectedModalItem: {
    backgroundColor: '#E8F5E9',
  },
  modalIconContainer: {
    width: 36,
    height: 36,
    borderRadius: 18,
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 12,
  },
  modalItemContent: {
    flex: 1,
  },
  modalItemText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  modalItemSubtext: {
    fontSize: 12,
    color: '#718096',
    marginTop: 2,
  },
  modalSeparator: {
    height: 1,
    backgroundColor: '#EDF2F7',
  },
  modalCloseButton: {
    backgroundColor: '#EDF2F7',
    borderRadius: 12,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 16,
  },
  modalCloseButtonText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#4A5568',
  },
  emptyContainer: {
    alignItems: 'center',
    paddingVertical: 40,
  },
  emptyText: {
    marginTop: 12,
    fontSize: 13,
    color: '#718096',
  },
});
