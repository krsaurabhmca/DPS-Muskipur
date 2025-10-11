import { FontAwesome5 } from '@expo/vector-icons';
import axios from 'axios';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  FlatList,
  Modal,
  SafeAreaView,
  ScrollView,
  StatusBar,
  StyleSheet,
  Switch,
  Text,
  TextInput,
  TouchableOpacity,
  View
} from 'react-native';

// Class and Section options
const CLASS_OPTIONS = [
  { label: 'NUR', value: 'NUR' },
  { label: 'LKG', value: 'LKG' },
  { label: 'UKG', value: 'UKG' },
  { label: 'I', value: 'I' },
  { label: 'II', value: 'II' },
  { label: 'III', value: 'III' },
  { label: 'IV', value: 'IV' },
  { label: 'V', value: 'V' },
  { label: 'VI', value: 'VI' },
  { label: 'VII', value: 'VII' },
  { label: 'VIII', value: 'VIII' },
  { label: 'IX', value: 'IX' },
  { label: 'X', value: 'X' }
];

const SECTION_OPTIONS = [
  { label: 'Section A', value: 'A' },
  { label: 'Section B', value: 'B' },
  { label: 'Section C', value: 'C' }
];

// Month names for date picker
const MONTHS = [
  'January', 'February', 'March', 'April', 'May', 'June', 
  'July', 'August', 'September', 'October', 'November', 'December'
];

export default function AttendanceScreen() {
  const router = useRouter();
  
  // State for class and section selection
  const [selectedClass, setSelectedClass] = useState('II');
  const [selectedSection, setSelectedSection] = useState('A');
  const [showClassModal, setShowClassModal] = useState(false);
  const [showSectionModal, setShowSectionModal] = useState(false);
  
  // State for date and attendance
  const [selectedDate, setSelectedDate] = useState(new Date());
  const [showDateModal, setShowDateModal] = useState(false);
  const [tempDate, setTempDate] = useState(new Date());
  const [students, setStudents] = useState([]);
  const [filteredStudents, setFilteredStudents] = useState([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [attendance, setAttendance] = useState({});
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [isEditMode, setIsEditMode] = useState(true); // Start in edit mode
  
  // Summary state
  const [summary, setSummary] = useState({
    total: 0,
    present: 0,
    absent: 0,
    percentPresent: 0
  });
  
  // Filter state
  const [showOnlyAbsent, setShowOnlyAbsent] = useState(false);
  const [showSummary, setShowSummary] = useState(true);
  
  // Effects for handling class/section changes and fetching students
  useEffect(() => {
    fetchStudents();
  }, [selectedClass, selectedSection]);
  
  // Effect for filtering students based on search and attendance
  useEffect(() => {
    filterStudents();
  }, [searchQuery, students, attendance, showOnlyAbsent]);
  
  // Effect for calculating attendance summary
  useEffect(() => {
    calculateSummary();
  }, [attendance, students]);
  
  const fetchStudents = async () => {
    setIsLoading(true);
    try {
      const response = await axios.post(
        'https://dpsmushkipur.com/bine/api.php?task=student_list',
        {
          student_class: selectedClass,
          student_section: selectedSection
        }
      );
      
      if (response.data.status === 'success') {
        // Initialize students with attendance status (default to present)
        const studentsWithStatus = response.data.data.map(student => ({
          ...student,
          isPresent: true // Default to present
        }));
        
        setStudents(studentsWithStatus);
        
        // Initialize attendance object for all students (present by default)
        const newAttendance = {};
        studentsWithStatus.forEach(student => {
          newAttendance[student.id] = true;
        });
        
        setAttendance(newAttendance);
      } else {
        Alert.alert('Error', 'Failed to load student list');
      }
    } catch (error) {
      console.error('Error fetching students:', error);
      Alert.alert('Error', 'An error occurred while fetching student list');
    } finally {
      setIsLoading(false);
    }
  };
  
  const filterStudents = () => {
    let filtered = [...students];
    
    // Apply search filter
    if (searchQuery.trim() !== '') {
      filtered = filtered.filter(student => 
        student.student_name.toLowerCase().includes(searchQuery.toLowerCase()) ||
        student.student_roll.includes(searchQuery) ||
        student.student_admission.includes(searchQuery)
      );
    }
    
    // Apply attendance filter (show only absent)
    if (showOnlyAbsent) {
      filtered = filtered.filter(student => !attendance[student.id]);
    }
    
    // Sort by roll number
    filtered.sort((a, b) => parseInt(a.student_roll) - parseInt(b.student_roll));
    
    setFilteredStudents(filtered);
  };
  
  const calculateSummary = () => {
    const total = students.length;
    const present = Object.values(attendance).filter(status => status).length;
    const absent = total - present;
    const percentPresent = total > 0 ? Math.round((present / total) * 100) : 0;
    
    setSummary({
      total,
      present,
      absent,
      percentPresent
    });
  };
  
  const toggleAttendance = (studentId) => {
    // Toggle student attendance status
    setAttendance(prev => ({
      ...prev,
      [studentId]: !prev[studentId]
    }));
  };
  
  const formatDate = (date) => {
    return `${date.getDate()} ${MONTHS[date.getMonth()]} ${date.getFullYear()}`;
  };
  
  const openDatePicker = () => {
    setTempDate(new Date(selectedDate));
    setShowDateModal(true);
  };
  
  const applySelectedDate = () => {
    setSelectedDate(tempDate);
    setShowDateModal(false);
  };
  
  const generateDaysForMonth = (year, month) => {
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const days = [];
    
    for (let i = 1; i <= daysInMonth; i++) {
      days.push(i);
    }
    
    return days;
  };
  
  const markAllPresent = () => {
    const newAttendance = {};
    students.forEach(student => {
      newAttendance[student.id] = true;
    });
    
    setAttendance(newAttendance);
  };
  
  const markAllAbsent = () => {
    const newAttendance = {};
    students.forEach(student => {
      newAttendance[student.id] = false;
    });
    
    setAttendance(newAttendance);
  };
  
  const submitAttendance = async () => {
    setIsSubmitting(true);
    
    try {
      // Format data for submission
      const student_list = {};
      Object.entries(attendance).forEach(([studentId, isPresent]) => {
        student_list[studentId] = isPresent ? "P" : "A";
      });
      
      const requestData = {
        action: "make_att",
        att_date: selectedDate.toISOString().split('T')[0],
        student_list: student_list
      };
      
      console.log('Submitting attendance data:', requestData);
      
      // Make API call
      const response = await axios.post(
        'https://dpsmushkipur.com/bine/api.php?task=make_att',
        requestData
      );
      
      // Check if response was successful
      if (response.data) {
        // Show success message with response data
        Alert.alert(
          'Attendance Submitted',
          `Successfully marked attendance for ${selectedClass}-${selectedSection}.\n\nPresent: ${response.data.present}\nAbsent: ${response.data.absent}`,
          [{ text: 'OK' }]
        );
        
        // Set edit mode to false after submission
        setIsEditMode(false);
      } else {
        throw new Error("Invalid response from server");
      }
    } catch (error) {
      console.error('Error submitting attendance:', error);
      Alert.alert(
        'Submission Failed',
        'There was an error submitting the attendance. Please try again.',
        [{ text: 'OK' }]
      );
    } finally {
      setIsSubmitting(false);
    }
  };
  
  const renderStudentItem = ({ item }) => {
    const isPresent = attendance[item.id];
    
    return (
      <View style={[
        styles.studentCard,
        isPresent ? styles.presentCard : styles.absentCard
      ]}>
        <View style={styles.studentInfo}>
          <View style={styles.rollContainer}>
            <Text style={styles.rollNumber}>{item.student_roll}</Text>
          </View>
          
          <View style={styles.nameContainer}>
            <Text style={styles.studentName}>{item.student_name}</Text>
            <Text style={styles.admissionNumber}>ID: {item.student_admission}</Text>
          </View>
        </View>
        
        <TouchableOpacity 
          style={[
            styles.attendanceToggle,
            isPresent ? styles.presentToggle : styles.absentToggle
          ]}
          onPress={() => toggleAttendance(item.id)}
          disabled={!isEditMode}
          activeOpacity={isEditMode ? 0.6 : 1}
        >
          {isPresent ? (
            <>
              <FontAwesome5 name="check-circle" size={16} color="#ffffff" />
              <Text style={styles.statusText}>Present</Text>
            </>
          ) : (
            <>
              <FontAwesome5 name="times-circle" size={16} color="#ffffff" />
              <Text style={styles.statusText}>Absent</Text>
            </>
          )}
        </TouchableOpacity>
      </View>
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#1e3c72" />
      
      {/* Fixed Header */}
      <View style={styles.header}>
        <LinearGradient
          colors={['#1e3c72', '#2a5298']}
          style={styles.headerGradient}
        >
          <View style={styles.headerContent}>
            <TouchableOpacity 
              style={styles.backButton}
              onPress={() => router.back()}
            >
              <FontAwesome5 name="arrow-left" size={20} color="#ffffff" />
            </TouchableOpacity>
            
            <Text style={styles.headerTitle}>Attendance</Text>
            
            <TouchableOpacity 
              style={[
                styles.editButton,
                isEditMode ? styles.activeEditButton : {}
              ]}
              onPress={() => setIsEditMode(!isEditMode)}
            >
              <FontAwesome5 
                name={isEditMode ? "check" : "edit"} 
                size={18} 
                color="#ffffff" 
              />
            </TouchableOpacity>
          </View>
        </LinearGradient>
      </View>
      
      {/* Fixed Class Selection Bar */}
      <View style={styles.classSelectionBar}>
        <View style={styles.classSelectionLeft}>
          <TouchableOpacity 
            style={styles.classSelector}
            onPress={() => setShowClassModal(true)}
          >
            <Text style={styles.classSelectorLabel}>Class</Text>
            <View style={styles.classSelectorValue}>
              <Text style={styles.selectedValueText}>{selectedClass}</Text>
              <FontAwesome5 name="chevron-down" size={12} color="#7f8c8d" />
            </View>
          </TouchableOpacity>
          
          <TouchableOpacity 
            style={styles.classSelector}
            onPress={() => setShowSectionModal(true)}
          >
            <Text style={styles.classSelectorLabel}>Section</Text>
            <View style={styles.classSelectorValue}>
              <Text style={styles.selectedValueText}>{selectedSection}</Text>
              <FontAwesome5 name="chevron-down" size={12} color="#7f8c8d" />
            </View>
          </TouchableOpacity>
        </View>
        
        <TouchableOpacity 
          style={styles.dateSelector}
          onPress={openDatePicker}
        >
          <Text style={styles.dateSelectorText}>{formatDate(selectedDate)}</Text>
          <FontAwesome5 name="calendar-alt" size={14} color="#1e3c72" />
        </TouchableOpacity>
      </View>
      
      {/* Toggle Summary Button */}
      <TouchableOpacity 
        style={styles.toggleSummaryButton}
        onPress={() => setShowSummary(!showSummary)}
      >
        <Text style={styles.toggleSummaryText}>
          {showSummary ? 'Hide Summary' : 'Show Summary'}
        </Text>
        <FontAwesome5 
          name={showSummary ? 'chevron-up' : 'chevron-down'} 
          size={14} 
          color="#7f8c8d" 
        />
      </TouchableOpacity>
      
      {/* Collapsible Summary Section */}
      {showSummary && (
        <>
          {/* Summary Cards */}
          <View style={styles.summaryRow}>
            <View style={styles.summaryCard}>
              <Text style={styles.summaryValue}>{summary.total}</Text>
              <Text style={styles.summaryLabel}>Total</Text>
            </View>
            
            <View style={styles.summaryCard}>
              <Text style={[styles.summaryValue, styles.presentValue]}>
                {summary.present}
              </Text>
              <Text style={styles.summaryLabel}>Present</Text>
            </View>
            
            <View style={styles.summaryCard}>
              <Text style={[styles.summaryValue, styles.absentValue]}>
                {summary.absent}
              </Text>
              <Text style={styles.summaryLabel}>Absent</Text>
            </View>
            
            <View style={styles.summaryCard}>
              <Text style={[styles.summaryValue, styles.percentValue]}>
                {summary.percentPresent}%
              </Text>
              <Text style={styles.summaryLabel}>Attendance</Text>
            </View>
          </View>
          
          {/* Filter and Action Row */}
          <View style={styles.actionRow}>
            <View style={styles.searchContainer}>
              <FontAwesome5 name="search" size={14} color="#7f8c8d" />
              <TextInput
                style={styles.searchInput}
                placeholder="Search..."
                value={searchQuery}
                onChangeText={setSearchQuery}
                placeholderTextColor="#95a5a6"
              />
              {searchQuery.length > 0 && (
                <TouchableOpacity onPress={() => setSearchQuery('')}>
                  <FontAwesome5 name="times" size={14} color="#7f8c8d" />
                </TouchableOpacity>
              )}
            </View>
            
            <View style={styles.filterContainer}>
              <Text style={styles.filterLabel}>Absent</Text>
              <Switch
                value={showOnlyAbsent}
                onValueChange={setShowOnlyAbsent}
                trackColor={{ false: '#ecf0f1', true: '#3498db' }}
                thumbColor={showOnlyAbsent ? '#2980b9' : '#ffffff'}
              />
            </View>
          </View>
          
          {/* Mark All Buttons */}
          {isEditMode && (
            <View style={styles.markAllContainer}>
              <TouchableOpacity 
                style={[styles.markAllButton, styles.markPresentButton]}
                onPress={markAllPresent}
              >
                <FontAwesome5 name="check-double" size={14} color="#ffffff" />
                <Text style={styles.markAllText}>All Present</Text>
              </TouchableOpacity>
              
              <TouchableOpacity 
                style={[styles.markAllButton, styles.markAbsentButton]}
                onPress={markAllAbsent}
              >
                <FontAwesome5 name="times" size={14} color="#ffffff" />
                <Text style={styles.markAllText}>All Absent</Text>
              </TouchableOpacity>
            </View>
          )}
        </>
      )}
      
      {/* Student List */}
      {isLoading ? (
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color="#1e3c72" />
          <Text style={styles.loadingText}>Loading students...</Text>
        </View>
      ) : filteredStudents.length > 0 ? (
        <FlatList
          data={filteredStudents}
          renderItem={renderStudentItem}
          keyExtractor={item => item.id}
          contentContainerStyle={styles.studentListContainer}
          showsVerticalScrollIndicator={true}
        />
      ) : (
        <View style={styles.emptyContainer}>
          <FontAwesome5 name="user-graduate" size={50} color="#e0e0e0" />
          <Text style={styles.emptyText}>No students found</Text>
        </View>
      )}
      
      {/* Submit Button */}
      {isEditMode && (
        <View style={styles.submitButtonContainer}>
          <TouchableOpacity 
            style={styles.submitButton}
            onPress={submitAttendance}
            disabled={isSubmitting}
          >
            {isSubmitting ? (
              <ActivityIndicator size="small" color="#ffffff" />
            ) : (
              <Text style={styles.submitButtonText}>Submit Attendance</Text>
            )}
          </TouchableOpacity>
        </View>
      )}
      
      {/* Class Selection Modal */}
      <Modal
        visible={showClassModal}
        transparent={true}
        animationType="fade"
        onRequestClose={() => setShowClassModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContainer}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Select Class</Text>
              <TouchableOpacity onPress={() => setShowClassModal(false)}>
                <FontAwesome5 name="times" size={20} color="#7f8c8d" />
              </TouchableOpacity>
            </View>
            
            <View style={styles.modalContent}>
              {CLASS_OPTIONS.map(option => (
                <TouchableOpacity 
                  key={option.value}
                  style={[
                    styles.modalOption,
                    selectedClass === option.value && styles.modalOptionSelected
                  ]}
                  onPress={() => {
                    setSelectedClass(option.value);
                    setShowClassModal(false);
                  }}
                >
                  <Text style={[
                    styles.modalOptionText,
                    selectedClass === option.value && styles.modalOptionTextSelected
                  ]}>
                    {option.label}
                  </Text>
                  {selectedClass === option.value && (
                    <FontAwesome5 name="check" size={16} color="#2ecc71" />
                  )}
                </TouchableOpacity>
              ))}
            </View>
          </View>
        </View>
      </Modal>
      
      {/* Section Selection Modal */}
      <Modal
        visible={showSectionModal}
        transparent={true}
        animationType="fade"
        onRequestClose={() => setShowSectionModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContainer}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Select Section</Text>
              <TouchableOpacity onPress={() => setShowSectionModal(false)}>
                <FontAwesome5 name="times" size={20} color="#7f8c8d" />
              </TouchableOpacity>
            </View>
            
            <View style={styles.modalContent}>
              {SECTION_OPTIONS.map(option => (
                <TouchableOpacity 
                  key={option.value}
                  style={[
                    styles.modalOption,
                    selectedSection === option.value && styles.modalOptionSelected
                  ]}
                  onPress={() => {
                    setSelectedSection(option.value);
                    setShowSectionModal(false);
                  }}
                >
                  <Text style={[
                    styles.modalOptionText,
                    selectedSection === option.value && styles.modalOptionTextSelected
                  ]}>
                    {option.label}
                  </Text>
                  {selectedSection === option.value && (
                    <FontAwesome5 name="check" size={16} color="#2ecc71" />
                  )}
                </TouchableOpacity>
              ))}
            </View>
          </View>
        </View>
      </Modal>
      
      {/* Date Selection Modal */}
      <Modal
        visible={showDateModal}
        transparent={true}
        animationType="fade"
        onRequestClose={() => setShowDateModal(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContainer}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Select Date</Text>
              <TouchableOpacity onPress={() => setShowDateModal(false)}>
                <FontAwesome5 name="times" size={20} color="#7f8c8d" />
              </TouchableOpacity>
            </View>
            
            <View style={styles.dateModalContent}>
              {/* Year and Month Selection */}
              <View style={styles.yearMonthSelection}>
                <View style={styles.yearSelector}>
                  <Text style={styles.yearMonthLabel}>Year</Text>
                  <View style={styles.yearPickerContainer}>
                    <TouchableOpacity 
                      style={styles.yearArrow}
                      onPress={() => {
                        const newDate = new Date(tempDate);
                        newDate.setFullYear(newDate.getFullYear() - 1);
                        setTempDate(newDate);
                      }}
                    >
                      <FontAwesome5 name="chevron-left" size={16} color="#7f8c8d" />
                    </TouchableOpacity>
                    
                    <Text style={styles.yearValue}>{tempDate.getFullYear()}</Text>
                    
                    <TouchableOpacity 
                      style={styles.yearArrow}
                      onPress={() => {
                        const newDate = new Date(tempDate);
                        const newYear = newDate.getFullYear() + 1;
                        // Don't allow selecting future years
                        if (newYear <= new Date().getFullYear()) {
                          newDate.setFullYear(newYear);
                          setTempDate(newDate);
                        }
                      }}
                      disabled={tempDate.getFullYear() >= new Date().getFullYear()}
                    >
                      <FontAwesome5 
                        name="chevron-right" 
                        size={16} 
                        color={tempDate.getFullYear() >= new Date().getFullYear() ? '#d0d0d0' : '#7f8c8d'} 
                      />
                    </TouchableOpacity>
                  </View>
                </View>
                
                <View style={styles.monthSelector}>
                  <Text style={styles.yearMonthLabel}>Month</Text>
                  <View style={styles.monthPickerContainer}>
                    <TouchableOpacity 
                      style={styles.monthArrow}
                      onPress={() => {
                        const newDate = new Date(tempDate);
                        newDate.setMonth(newDate.getMonth() - 1);
                        setTempDate(newDate);
                      }}
                    >
                      <FontAwesome5 name="chevron-left" size={16} color="#7f8c8d" />
                    </TouchableOpacity>
                    
                    <Text style={styles.monthValue}>
                      {MONTHS[tempDate.getMonth()]}
                    </Text>
                    
                    <TouchableOpacity 
                      style={styles.monthArrow}
                      onPress={() => {
                        const newDate = new Date(tempDate);
                        const currentDate = new Date();
                        
                        // Don't allow selecting future months in current year
                        if (
                          tempDate.getFullYear() < currentDate.getFullYear() ||
                          (tempDate.getFullYear() === currentDate.getFullYear() && 
                           tempDate.getMonth() < currentDate.getMonth())
                        ) {
                          newDate.setMonth(newDate.getMonth() + 1);
                          setTempDate(newDate);
                        }
                      }}
                      disabled={
                        tempDate.getFullYear() === new Date().getFullYear() && 
                        tempDate.getMonth() >= new Date().getMonth()
                      }
                    >
                      <FontAwesome5 
                        name="chevron-right" 
                        size={16} 
                        color={
                          tempDate.getFullYear() === new Date().getFullYear() && 
                          tempDate.getMonth() >= new Date().getMonth()
                            ? '#d0d0d0' 
                            : '#7f8c8d'
                        } 
                      />
                    </TouchableOpacity>
                  </View>
                </View>
              </View>
              
              {/* Day Grid */}
              <View style={styles.dayGrid}>
                <View style={styles.weekdayHeader}>
                  <Text style={styles.weekdayLabel}>Sun</Text>
                  <Text style={styles.weekdayLabel}>Mon</Text>
                  <Text style={styles.weekdayLabel}>Tue</Text>
                  <Text style={styles.weekdayLabel}>Wed</Text>
                  <Text style={styles.weekdayLabel}>Thu</Text>
                  <Text style={styles.weekdayLabel}>Fri</Text>
                  <Text style={styles.weekdayLabel}>Sat</Text>
                </View>
                
                <ScrollView style={styles.daysContainer}>
                  <View style={styles.daysGrid}>
                    {(() => {
                      const year = tempDate.getFullYear();
                      const month = tempDate.getMonth();
                      const daysInMonth = generateDaysForMonth(year, month);
                      const firstDayOfMonth = new Date(year, month, 1).getDay();
                      
                      const currentDate = new Date();
                      const dayElements = [];
                      
                      // Add empty spaces for days before the 1st of the month
                      for (let i = 0; i < firstDayOfMonth; i++) {
                        dayElements.push(
                          <View key={`empty-${i}`} style={styles.emptyDay} />
                        );
                      }
                      
                      // Add actual days of the month
                      for (let day of daysInMonth) {
                        const date = new Date(year, month, day);
                        const isCurrentDate = date.getDate() === tempDate.getDate() &&
                                            date.getMonth() === tempDate.getMonth() &&
                                            date.getFullYear() === tempDate.getFullYear();
                        const isFutureDate = date > currentDate;
                        
                        dayElements.push(
                          <TouchableOpacity 
                            key={`day-${day}`}
                            style={[
                              styles.dayButton,
                              isCurrentDate && styles.selectedDayButton,
                              isFutureDate && styles.disabledDayButton
                            ]}
                            onPress={() => {
                              if (!isFutureDate) {
                                const newDate = new Date(tempDate);
                                newDate.setDate(day);
                                setTempDate(newDate);
                              }
                            }}
                            disabled={isFutureDate}
                          >
                            <Text style={[
                              styles.dayText,
                              isCurrentDate && styles.selectedDayText,
                              isFutureDate && styles.disabledDayText
                            ]}>
                              {day}
                            </Text>
                          </TouchableOpacity>
                        );
                      }
                      
                      return dayElements;
                    })()}
                  </View>
                </ScrollView>
              </View>
              
              {/* Date picker footer */}
              <View style={styles.datePickerFooter}>
                <TouchableOpacity 
                  style={styles.cancelDateButton}
                  onPress={() => setShowDateModal(false)}
                >
                  <Text style={styles.cancelDateText}>Cancel</Text>
                </TouchableOpacity>
                
                <TouchableOpacity 
                  style={styles.applyDateButton}
                  onPress={applySelectedDate}
                >
                  <Text style={styles.applyDateText}>Apply</Text>
                </TouchableOpacity>
              </View>
            </View>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f6fa',
  },
  header: {
    height: 75,
    width: '100%',
  },
  headerGradient: {
    flex: 1,
    paddingTop: 10,
  },
  headerContent: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingTop: 10,
  },
  backButton: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#ffffff',
  },
  editButton: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  activeEditButton: {
    backgroundColor: 'rgba(46, 204, 113, 0.5)',
  },
  classSelectionBar: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    paddingHorizontal: 16,
    paddingVertical: 12,
    marginBottom: 8,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
  },
  classSelectionLeft: {
    flexDirection: 'row',
  },
  classSelector: {
    marginRight: 16,
  },
  classSelectorLabel: {
    fontSize: 12,
    color: '#7f8c8d',
    marginBottom: 4,
  },
  classSelectorValue: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  selectedValueText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginRight: 6,
  },
  dateSelector: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f5f6fa',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 6,
  },
  dateSelectorText: {
    fontSize: 12,
    color: '#2c3e50',
    marginRight: 6,
  },
  toggleSummaryButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    borderBottomWidth: 1,
    borderBottomColor: '#ecf0f1',
    backgroundColor: '#ffffff',
  },
  toggleSummaryText: {
    fontSize: 14,
    color: '#7f8c8d',
    marginRight: 6,
  },
  summaryRow: {
    flexDirection: 'row',
    paddingHorizontal: 8,
    paddingVertical: 12,
    backgroundColor: '#ffffff',
  },
  summaryCard: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 8,
    paddingHorizontal: 4,
  },
  summaryValue: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginBottom: 4,
  },
  presentValue: {
    color: '#27ae60',
  },
  absentValue: {
    color: '#e74c3c',
  },
  percentValue: {
    color: '#3498db',
  },
  summaryLabel: {
    fontSize: 11,
    color: '#7f8c8d',
  },
  actionRow: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 16,
    paddingVertical: 10,
    backgroundColor: '#ffffff',
  },
  searchContainer: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#f5f6fa',
    borderRadius: 8,
    paddingHorizontal: 10,
    paddingVertical: 8,
    marginRight: 8,
  },
  searchInput: {
    flex: 1,
    fontSize: 14,
    marginLeft: 8,
    color: '#2c3e50',
    padding: 0,
  },
  filterContainer: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  filterLabel: {
    fontSize: 12,
    color: '#7f8c8d',
    marginRight: 6,
  },
  markAllContainer: {
    flexDirection: 'row',
    paddingHorizontal: 16,
    paddingVertical: 10,
    paddingBottom: 16,
    backgroundColor: '#ffffff',
    borderBottomWidth: 1,
    borderBottomColor: '#ecf0f1',
  },
  markAllButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: 8,
    marginHorizontal: 4,
  },
  markPresentButton: {
    backgroundColor: '#27ae60',
  },
  markAbsentButton: {
    backgroundColor: '#e74c3c',
  },
  markAllText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#ffffff',
    marginLeft: 6,
  },
  studentListContainer: {
    padding: 16,
    paddingBottom: 100, // Space for submit button
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 12,
    fontSize: 14,
    color: '#7f8c8d',
  },
  emptyContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  emptyText: {
    marginTop: 16,
    fontSize: 16,
    color: '#7f8c8d',
  },
  studentCard: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    backgroundColor: '#ffffff',
    borderRadius: 10,
    padding: 12,
    marginBottom: 10,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    elevation: 2,
    borderLeftWidth: 4,
  },
  presentCard: {
    borderLeftColor: '#27ae60',
  },
  absentCard: {
    borderLeftColor: '#e74c3c',
  },
  studentInfo: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
  },
  rollContainer: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: '#f5f6fa',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 10,
  },
  rollNumber: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#34495e',
  },
  nameContainer: {
    flex: 1,
  },
  studentName: {
    fontSize: 14,
    fontWeight: '600',
    color: '#2c3e50',
  },
  admissionNumber: {
    fontSize: 12,
    color: '#7f8c8d',
    marginTop: 2,
  },
  attendanceToggle: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 6,
  },
  presentToggle: {
    backgroundColor: '#27ae60',
  },
  absentToggle: {
    backgroundColor: '#e74c3c',
  },
  statusText: {
    color: '#ffffff',
    fontWeight: '600',
    fontSize: 12,
    marginLeft: 4,
  },
  submitButtonContainer: {
    position: 'absolute',
    bottom: 16,
    left: 16,
    right: 16,
  },
  submitButton: {
    backgroundColor: '#1e3c72',
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 3,
    elevation: 4,
  },
  submitButtonText: {
    color: '#ffffff',
    fontWeight: 'bold',
    fontSize: 16,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalContainer: {
    width: '90%',
    maxHeight: '80%',
    backgroundColor: '#ffffff',
    borderRadius: 12,
    overflow: 'hidden',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 5,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#ecf0f1',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  modalContent: {
    maxHeight: '70%',
  },
  modalOption: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: 16,
    paddingHorizontal: 20,
    borderBottomWidth: 1,
    borderBottomColor: '#f5f6fa',
  },
  modalOptionSelected: {
    backgroundColor: '#f8f9fa',
  },
  modalOptionText: {
    fontSize: 16,
    color: '#2c3e50',
  },
  modalOptionTextSelected: {
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  // Date Picker Modal Styles
  dateModalContent: {
    padding: 16,
  },
  yearMonthSelection: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 16,
  },
  yearSelector: {
    flex: 1,
    marginRight: 8,
  },
  monthSelector: {
    flex: 1,
    marginLeft: 8,
  },
  yearMonthLabel: {
    fontSize: 14,
    color: '#7f8c8d',
    marginBottom: 8,
  },
  yearPickerContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#f5f6fa',
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
  },
  monthPickerContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#f5f6fa',
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
  },
  yearArrow: {
    padding: 4,
  },
  monthArrow: {
    padding: 4,
  },
  yearValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  monthValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: '#2c3e50',
    flex: 1,
    textAlign: 'center',
  },
  dayGrid: {
    marginBottom: 16,
  },
  weekdayHeader: {
    flexDirection: 'row',
    marginBottom: 8,
  },
  weekdayLabel: {
    flex: 1,
    fontSize: 12,
    color: '#7f8c8d',
    textAlign: 'center',
  },
  daysContainer: {
    maxHeight: 240,
  },
  daysGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
  },
  dayButton: {
    width: `${100 / 7}%`,
    aspectRatio: 1,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 8,
  },
  selectedDayButton: {
    backgroundColor: '#3498db',
    borderRadius: 20,
  },
  disabledDayButton: {
    opacity: 0.3,
  },
  emptyDay: {
    width: `${100 / 7}%`,
    aspectRatio: 1,
  },
  dayText: {
    fontSize: 14,
    color: '#2c3e50',
  },
  selectedDayText: {
    color: '#ffffff',
    fontWeight: 'bold',
  },
  disabledDayText: {
    color: '#95a5a6',
  },
  datePickerFooter: {
    flexDirection: 'row',
    justifyContent: 'flex-end',
    borderTopWidth: 1,
    borderTopColor: '#ecf0f1',
    paddingTop: 16,
  },
  cancelDateButton: {
    paddingVertical: 10,
    paddingHorizontal: 16,
    marginRight: 8,
  },
  cancelDateText: {
    fontSize: 14,
    color: '#7f8c8d',
  },
  applyDateButton: {
    backgroundColor: '#1e3c72',
    paddingVertical: 10,
    paddingHorizontal: 16,
    borderRadius: 8,
  },
  applyDateText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#ffffff',
  }
});