// HomeWork.js
import { FontAwesome5, Ionicons } from '@expo/vector-icons';
import { format } from 'date-fns';
import { StatusBar } from 'expo-status-bar';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  Animated,
  Dimensions,
  FlatList,
  Image,
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

const { width, height } = Dimensions.get('window');
import {
  SafeAreaProvider,
  SafeAreaView,
  useSafeAreaInsets
} from 'react-native-safe-area-context';

// Import the FileUploader component
import { LinearGradient } from 'expo-linear-gradient';
import { router } from 'expo-router';
import FileUploader from './FileUploader';

export default function HomeWork() {
  // Get safe area insets
  const insets = useSafeAreaInsets();
  
  // Role & Assignments states
  const [userRole, setUserRole] = useState('ADMIN');
  const [assignments, setAssignments] = useState([]);
  
  // States for class selection
  const [classes, setClasses] = useState([]);
  const [selectedClass, setSelectedClass] = useState(null);
  const [classModalVisible, setClassModalVisible] = useState(false);
  const [loadingClasses, setLoadingClasses] = useState(false);
  
  // States for subject selection
  const [subjects, setSubjects] = useState([]);
  const [selectedSubject, setSelectedSubject] = useState(null);
  const [subjectModalVisible, setSubjectModalVisible] = useState(false);
  const [loadingSubjects, setLoadingSubjects] = useState(false);
  
  // States for homework details
  const [homeworkText, setHomeworkText] = useState('');
  const [uploadedFileInfo, setUploadedFileInfo] = useState(null);
  const [submitting, setSubmitting] = useState(false);
  const [currentDate] = useState(new Date());
  const [submitSuccess, setSubmitSuccess] = useState(false);
  
  const formattedDate = format(currentDate, 'dd MMMM yyyy');
  
  // Tab states and history lists
  const [userId, setUserId] = useState('');
  const [activeTab, setActiveTab] = useState('assign'); // 'assign' or 'history'
  const [historyLogs, setHistoryLogs] = useState([]);
  const [loadingHistory, setLoadingHistory] = useState(false);
  const [selectedImage, setSelectedImage] = useState(null);
  const [imageModalVisible, setImageModalVisible] = useState(false);

  // Homework history query
  const fetchHomeworkHistory = async (uId = userId, role = userRole) => {
    try {
      setLoadingHistory(true);
      const activeUid = uId || await AsyncStorage.getItem('user_id');
      const activeRole = role || await AsyncStorage.getItem('user_type');
      
      const response = await fetch('https://dpsmushkipur.com/bine/api.php?task=get_teacher_homework_history', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          user_id: activeUid,
          role: activeRole
        })
      });
      const result = await response.json();
      if (result.status === 'success') {
        setHistoryLogs(result.data || []);
      }
    } catch (err) {
      console.error('Error fetching homework history:', err);
    } finally {
      setLoadingHistory(false);
    }
  };

  // Animation values
  const [fadeAnim] = useState(new Animated.Value(0));

  // Animation effect on component mount
  useEffect(() => {
    Animated.timing(fadeAnim, {
      toValue: 1,
      duration: 800,
      useNativeDriver: true,
    }).start();
  }, []);

  // Fetch classes on component mount
  useEffect(() => {
    fetchClasses();
  }, []);

  // Fetch subjects when a class is selected
  useEffect(() => {
    if (selectedClass) {
      fetchSubjects();
    }
  }, [selectedClass]);

  // Reset form after successful submission
  useEffect(() => {
    if (submitSuccess) {
      const timer = setTimeout(() => {
        setSubmitSuccess(false);
      }, 3000);
      
      return () => clearTimeout(timer);
    }
  }, [submitSuccess]);

  const fetchClasses = async () => {
    try {
      setLoadingClasses(true);
      
      const role = await AsyncStorage.getItem('user_type');
      const uId = await AsyncStorage.getItem('user_id');
      setUserRole(role || 'TEACHER');
      setUserId(uId || '');
      
      // Load teacher homework history
      fetchHomeworkHistory(uId, role || 'TEACHER');
      
      if (role === 'TEACHER' || role === 'STAFF') {
        const response = await fetch('https://dpsmushkipur.com/bine/api.php?task=get_teacher_assignments', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: uId })
        });
        const result = await response.json();
        
        if (result.status === 'success' && Array.isArray(result.data)) {
          setAssignments(result.data);
          
          // Build list of unique assigned class-sections
          const uniqueClassesMap = {};
          result.data.forEach(item => {
            const key = `${item.student_class}-${item.student_section}`;
            if (!uniqueClassesMap[key]) {
              uniqueClassesMap[key] = {
                student_class: item.student_class,
                student_section: item.student_section,
                total: 0 // Default
              };
            }
          });
          setClasses(Object.values(uniqueClassesMap));
        } else {
          showAlert('Info', 'No active class assignments found for your teacher account.');
        }
      } else {
        const response = await fetch('https://dpsmushkipur.com/bine/api.php?task=class_list');
        const result = await response.json();
        
        if (result.status === 'success') {
          setClasses(result.data);
        } else {
          showAlert('Error', 'Failed to fetch classes');
        }
      }
    } catch (err) {
      showAlert('Error', 'Network error: ' + err.message);
    } finally {
      setLoadingClasses(false);
    }
  };

  const fetchSubjects = async () => {
    if (!selectedClass) return;
    
    try {
      setLoadingSubjects(true);
      
      const role = await AsyncStorage.getItem('user_type');
      if (role === 'TEACHER' || role === 'STAFF') {
        // Teacher flow: filter subjects from active assignments list
        const allowedSubjects = assignments
          .filter(a => a.student_class === selectedClass.student_class && a.student_section === selectedClass.student_section)
          .map(a => ({
            id: a.subject_id,
            subject_name: a.subject_name
          }));
        setSubjects(allowedSubjects);
      } else {
        // Admin flow: fetch all subjects for class
        const response = await fetch(
          'https://dpsmushkipur.com/bine/api.php?task=subject_list',
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ hw_class: selectedClass.student_class }),
          }
        );
        const result = await response.json();
        
        if (result.status === 'success') {
          setSubjects(result.data);
        } else {
          showAlert('Error', 'Failed to fetch subjects');
        }
      }
    } catch (err) {
      showAlert('Error', 'Network error: ' + err.message);
    } finally {
      setLoadingSubjects(false);
    }
  };

  const showAlert = (title, message) => {
    Alert.alert(
      title,
      message,
      [{ text: 'OK', style: 'default' }],
      { cancelable: true }
    );
  };

  // Handle successful file upload
  const handleUploadSuccess = (fileInfo) => {
    setUploadedFileInfo(fileInfo);
  };

  // Handle file upload error
  const handleUploadError = (error) => {
    showAlert('Upload Error', error);
    setUploadedFileInfo(null);
  };

  const resetForm = () => {
    setHomeworkText('');
    setUploadedFileInfo(null);
    setSelectedClass(null);
    setSelectedSubject(null);
  };

  const submitHomework = async () => {
    if (!selectedClass || !selectedSubject) {
      showAlert('Required Fields', 'Please select class and subject');
      return;
    }
    
    if (!homeworkText.trim()) {
      showAlert('Required Field', 'Please enter homework details');
      return;
    }

    try {
      setSubmitting(true);
      // Use the uploaded filename from the API response
      const filename = uploadedFileInfo?.file_name || '';
      
      const homeworkData = {
        task: "home_work",
        hw_class: selectedClass.student_class,
        hw_section: selectedClass.student_section,
        subject_id: selectedSubject.id,
        hw_text: homeworkText,
        hw_file: filename
      };

      console.log('Submitting homework with data:', homeworkData);

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=home_work',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(homeworkData),
        }
      );

      const result = await response.json();
      
      if (result.status === 'success') {
        setSubmitSuccess(true);
        resetForm();
        fetchHomeworkHistory(userId, userRole);
      } else {
        showAlert('Error', 'Failed to add homework: ' + (result.message || 'Unknown error'));
      }
    } catch (err) {
      showAlert('Error', 'Network error: ' + err.message);
    } finally {
      setSubmitting(false);
    }
  };

  // Class selection modal
  const ClassSelectionModal = () => (
    <Modal
      visible={classModalVisible}
      transparent={true}
      animationType="slide"
      statusBarTranslucent={true}
      onRequestClose={() => setClassModalVisible(false)}
    >
      <View style={[styles.modalOverlay, {paddingTop: insets.top}]}>
        <Pressable 
          style={{flex: 1}} 
          onPress={() => setClassModalVisible(false)}
        />
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <View style={styles.modalHandle} />
            <Text style={styles.modalTitle}>Select Class & Section</Text>
            {loadingClasses ? (
              <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color="#4a6ea9" />
                <Text style={styles.loadingText}>Loading classes...</Text>
              </View>
            ) : (
              <FlatList
                data={classes}
                keyExtractor={(item, index) => `${item.student_class}-${item.student_section}-${index}`}
                renderItem={({ item }) => (
                  <Pressable
                    style={({ pressed }) => [
                      styles.modalItem,
                      pressed && styles.modalItemPressed
                    ]}
                    onPress={() => {
                      setSelectedClass(item);
                      setClassModalVisible(false);
                      // Reset subject when class changes
                      setSelectedSubject(null);
                    }}
                    android_ripple={{ color: 'rgba(74, 110, 169, 0.1)' }}
                  >
                    <View style={styles.classIconContainer}>
                      <Text style={styles.classIcon}>{item.student_class}</Text>
                    </View>
                    <View style={styles.modalItemContent}>
                      <Text style={styles.modalItemText}>
                        Class {item.student_class} - Section {item.student_section}
                      </Text>
                      <Text style={styles.modalItemSubtext}>
                        {item.total} Students
                      </Text>
                    </View>
                  </Pressable>
                )}
                ItemSeparatorComponent={() => <View style={styles.modalSeparator} />}
                contentContainerStyle={styles.modalList}
              />
            )}
            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setClassModalVisible(false)}
              activeOpacity={0.7}
            >
              <Text style={styles.modalCloseButtonText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );

  // Subject selection modal
  const SubjectSelectionModal = () => (
    <Modal
      visible={subjectModalVisible}
      transparent={true}
      animationType="slide"
      statusBarTranslucent={true}
      onRequestClose={() => setSubjectModalVisible(false)}
    >
      <View style={[styles.modalOverlay, {paddingTop: insets.top}]}>
        <Pressable 
          style={{flex: 1}} 
          onPress={() => setSubjectModalVisible(false)}
        />
        <View style={styles.modalContainer}>
          <View style={styles.modalContent}>
            <View style={styles.modalHandle} />
            <Text style={styles.modalTitle}>Select Subject</Text>
            {loadingSubjects ? (
              <View style={styles.loadingContainer}>
                <ActivityIndicator size="large" color="#4a6ea9" />
                <Text style={styles.loadingText}>Loading subjects...</Text>
              </View>
            ) : subjects.length > 0 ? (
              <FlatList
                data={subjects}
                keyExtractor={(item) => item.id}
                renderItem={({ item }) => (
                  <Pressable
                    style={({ pressed }) => [
                      styles.modalItem,
                      pressed && styles.modalItemPressed
                    ]}
                    onPress={() => {
                      setSelectedSubject(item);
                      setSubjectModalVisible(false);
                    }}
                    android_ripple={{ color: 'rgba(74, 110, 169, 0.1)' }}
                  >
                    <View style={styles.subjectIconContainer}>
                      <Ionicons name="book-outline" size={20} color="#fff" />
                    </View>
                    <View style={styles.modalItemContent}>
                      <Text style={styles.modalItemText}>{item.subject_name}</Text>
                    </View>
                  </Pressable>
                )}
                ItemSeparatorComponent={() => <View style={styles.modalSeparator} />}
                contentContainerStyle={styles.modalList}
              />
            ) : (
              <View style={styles.noDataContainer}>
                <Ionicons name="alert-circle-outline" size={40} color="#999" />
                <Text style={styles.noDataText}>No subjects available for this class</Text>
              </View>
            )}
            <TouchableOpacity
              style={styles.modalCloseButton}
              onPress={() => setSubjectModalVisible(false)}
              activeOpacity={0.7}
            >
              <Text style={styles.modalCloseButtonText}>Cancel</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>
    </Modal>
  );

  // File information display component
  const FileInfoDisplay = () => {
    if (!uploadedFileInfo) return null;
    
    const isPdf = uploadedFileInfo.file_type === 'pdf';
    
    return (
      <View style={styles.fileInfoDisplay}>
        <View style={styles.fileInfoContent}>
          {isPdf ? (
            <View style={styles.pdfIconContainer}>
              <Ionicons name="document-text" size={24} color="white" />
            </View>
          ) : (
            <Image 
              source={{ uri: uploadedFileInfo.file_path }}
              style={styles.imagePreview}
              resizeMode="cover"
            />
          )}
          <View style={styles.fileDetails}>
            <Text style={styles.fileName} numberOfLines={1}>{uploadedFileInfo.file_name}</Text>
            <View style={styles.fileMetaContainer}>
              <Text style={styles.uploadedText}>
                <Ionicons name="checkmark-circle" size={12} color="#4caf50" /> Uploaded
              </Text>
              <Text style={styles.fileSize}>{uploadedFileInfo.file_size}</Text>
            </View>
          </View>
        </View>
        <TouchableOpacity 
          style={styles.removeFileButton}
          onPress={() => setUploadedFileInfo(null)}
          hitSlop={{ top: 10, right: 10, bottom: 10, left: 10 }}
        >
          <Ionicons name="close-circle" size={24} color="#f44336" />
        </TouchableOpacity>
      </View>
    );
  };

  // Success message component
  const SuccessMessage = () => {
    if (!submitSuccess) return null;
    
    return (
      <Animated.View 
        style={[
          styles.successMessage, 
          {
            opacity: fadeAnim,
            transform: [{ translateY: fadeAnim.interpolate({
              inputRange: [0, 1],
              outputRange: [20, 0]
            }) }]
          }
        ]}
      >
        <Ionicons name="checkmark-circle" size={24} color="white" />
        <Text style={styles.successText}>Homework assigned successfully!</Text>
      </Animated.View>
    );
  };

  // Render Homework History View
  const renderHistoryTab = () => {
    if (loadingHistory) {
      return (
        <View style={styles.loadingHistoryContainer}>
          <ActivityIndicator size="large" color="#1B5E20" />
          <Text style={styles.loadingHistoryText}>Loading homework history...</Text>
        </View>
      );
    }

    return (
      <FlatList
        data={historyLogs}
        keyExtractor={(item) => String(item.id)}
        contentContainerStyle={[styles.historyListContent, { paddingBottom: 20 + insets.bottom }]}
        showsVerticalScrollIndicator={false}
        ListEmptyComponent={() => (
          <View style={styles.emptyHistoryCard}>
            <Ionicons name="clipboard-outline" size={60} color="#CBD5E0" style={{ marginBottom: 14 }} />
            <Text style={styles.emptyHistoryTitle}>No homework logs found</Text>
            <Text style={styles.emptyHistoryDesc}>
              You have not posted any homework assignments yet. Use the Create Tab to post your first assignment!
            </Text>
          </View>
        )}
        renderItem={({ item }) => {
          const isImage = item.hw_file && ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(item.hw_file.split('.').pop().toLowerCase());
          const isPdf = item.hw_file && item.hw_file.split('.').pop().toLowerCase() === 'pdf';
          
          return (
            <View style={styles.historyCard}>
              <View style={styles.historyCardHeader}>
                <View style={styles.historyHeaderLeft}>
                  <View style={[styles.subjectIconCircle, { backgroundColor: '#E8F5E9' }]}>
                    <Ionicons name="book" size={18} color="#1B5E20" />
                  </View>
                  <View>
                    <Text style={styles.historySubjectText}>{item.subject_name || `Subject #${item.subject_id}`}</Text>
                    <Text style={styles.historyClassText}>Class {item.hw_class} - {item.hw_section}</Text>
                  </View>
                </View>
                <View style={styles.historyDateBadge}>
                  <Text style={styles.historyDateText}>
                    {new Date(item.hw_date).toLocaleDateString('en-US', {
                      month: 'short',
                      day: 'numeric',
                      year: 'numeric'
                    })}
                  </Text>
                </View>
              </View>

              <View style={styles.historyCardBody}>
                <Text style={styles.historyLabel}>Assignment details:</Text>
                <Text style={styles.historyDescriptionText}>{item.hw_text}</Text>
                
                {item.hw_file ? (
                  <TouchableOpacity
                    style={styles.historyAttachmentBtn}
                    onPress={() => {
                      const fileUrl = `https://dpsmushkipur.com/bine/homework/${item.hw_file}`;
                      if (isImage) {
                        setSelectedImage(fileUrl);
                        setImageModalVisible(true);
                      } else {
                        Linking.openURL(fileUrl).catch(() => {
                          Alert.alert('Error', 'Could not open attachment link.');
                        });
                      }
                    }}
                  >
                    <Ionicons 
                      name={isImage ? "image-outline" : isPdf ? "document-text-outline" : "attach-outline"} 
                      size={18} 
                      color="#1B5E20" 
                    />
                    <Text style={styles.historyAttachmentText} numberOfLines={1}>
                      {item.hw_file}
                    </Text>
                    <Ionicons name="eye-outline" size={14} color="#1B5E20" style={{ marginLeft: 'auto' }} />
                  </TouchableOpacity>
                ) : null}
              </View>
            </View>
          );
        }}
      />
    );
  };

  return (
    <SafeAreaProvider>
      <StatusBar style="dark" />
      <SafeAreaView style={styles.safeArea} edges={['top']}>
        <KeyboardAvoidingView
          behavior={Platform.OS === "ios" ? "padding" : "height"}
          style={{ flex: 1 }}
          keyboardVerticalOffset={Platform.OS === "ios" ? 0 : 25}
        >
        
          <View style={styles.header}>
            <LinearGradient
              colors={["#1e3c72", "#2a5298"]}
              style={styles.headerGradient}
            />
            <View style={styles.headerContent}>
              <TouchableOpacity
                style={styles.backButton}
                onPress={() => router.back()}
              >
                <FontAwesome5 name="arrow-left" size={20} color="#ffffff" />
              </TouchableOpacity>
              <Text style={styles.headerTitle}>HomeWork</Text>
              <TouchableOpacity
                style={styles.infoButton}
                onPress={() => {
                  Alert.alert(
                    "Homework Information",
                    "Assigned homework will be published to the selected student classes instantly.",
                    [{ text: "OK" }]
                  );
                }}
              >
                <FontAwesome5 name="info-circle" size={20} color="#ffffff" />
              </TouchableOpacity>
            </View>
          </View>

          {/* Tab Buttons */}
          <View style={styles.tabsHeader}>
            <TouchableOpacity
              style={[styles.tabBtn, activeTab === 'assign' && styles.activeTabBtn]}
              onPress={() => setActiveTab('assign')}
            >
              <Ionicons name="create-outline" size={16} color={activeTab === 'assign' ? '#fff' : '#1B5E20'} style={{ marginRight: 6 }} />
              <Text style={[styles.tabBtnText, activeTab === 'assign' && styles.activeTabBtnText]}>
                Assign Homework
              </Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.tabBtn, activeTab === 'history' && styles.activeTabBtn]}
              onPress={() => {
                setActiveTab('history');
                fetchHomeworkHistory(userId, userRole);
              }}
            >
              <Ionicons name="time-outline" size={16} color={activeTab === 'history' ? '#fff' : '#1B5E20'} style={{ marginRight: 6 }} />
              <Text style={[styles.tabBtnText, activeTab === 'history' && styles.activeTabBtnText]}>
                Homework History
              </Text>
            </TouchableOpacity>
          </View>
          
          {activeTab === 'assign' ? (
          <ScrollView 
            style={styles.container} 
            contentContainerStyle={[
              styles.contentContainer,
              { paddingBottom: 20 + insets.bottom }
            ]}
            showsVerticalScrollIndicator={false}
          >
            <Animated.View 
              style={[
                styles.card, 
                { opacity: fadeAnim }
              ]}
            >
              <View style={styles.cardHeader}>
                <Ionicons name="create-outline" size={22} color="#4a6ea9" />
                <Text style={styles.cardHeaderText}>Create New Assignment</Text>
              </View>
              
              {/* Class Selection */}
              <View style={styles.section}>
                <Text style={styles.sectionTitle}>Class and Section</Text>
                <TouchableOpacity
                  style={styles.selector}
                  onPress={() => setClassModalVisible(true)}
                  activeOpacity={0.7}
                >
                  <Ionicons 
                    name="people-outline" 
                    size={20} 
                    color={selectedClass ? "#4a6ea9" : "#999"} 
                    style={styles.selectorIcon}
                  />
                  <Text style={[
                    styles.selectorText,
                    !selectedClass && styles.selectorPlaceholder
                  ]}>
                    {selectedClass 
                      ? `Class ${selectedClass.student_class} - Section ${selectedClass.student_section}`
                      : 'Select class and section'}
                  </Text>
                  <Ionicons name="chevron-down" size={20} color="#999" />
                </TouchableOpacity>
              </View>
              
              {/* Subject Selection */}
              <View style={styles.section}>
                <Text style={styles.sectionTitle}>Subject</Text>
                <TouchableOpacity
                  style={[styles.selector, !selectedClass && styles.disabledSelector]}
                  onPress={() => {
                    if (selectedClass) setSubjectModalVisible(true);
                  }}
                  activeOpacity={selectedClass ? 0.7 : 1}
                  disabled={!selectedClass}
                >
                  <Ionicons 
                    name="book-outline" 
                    size={20} 
                    color={selectedSubject ? "#4a6ea9" : "#999"} 
                    style={styles.selectorIcon}
                  />
                  <Text style={[
                    styles.selectorText,
                    !selectedSubject && styles.selectorPlaceholder
                  ]}>
                    {selectedSubject 
                      ? selectedSubject.subject_name
                      : 'Select subject'}
                  </Text>
                  <Ionicons name="chevron-down" size={20} color="#999" />
                </TouchableOpacity>
              </View>
              
              {/* Date Display */}
              <View style={styles.section}>
                <Text style={styles.sectionTitle}>Date</Text>
                <View style={styles.dateContainer}>
                  <Ionicons name="calendar-outline" size={20} color="#4a6ea9" style={styles.selectorIcon} />
                  <Text style={styles.dateText}>{formattedDate}</Text>
                </View>
              </View>
              
              {/* Homework Content */}
              <View style={styles.section}>
                <Text style={styles.sectionTitle}>Homework Details</Text>
                <TextInput
                  style={[styles.textInput, !selectedSubject && styles.disabledInput]}
                  multiline
                  numberOfLines={6}
                  placeholder="Enter homework instructions here..."
                  value={homeworkText}
                  onChangeText={setHomeworkText}
                  editable={!!selectedSubject}
                  placeholderTextColor="#999"
                />
              </View>
              
              {/* File Attachment - Using FileUploader Component */}
              <View style={styles.section}>
                <Text style={styles.sectionTitle}>
                  Attachment (Optional)
                </Text>
                <Text style={styles.sectionSubtitle}>
                  PDF or Image up to 5MB
                </Text>
                
                {uploadedFileInfo ? (
                  <FileInfoDisplay />
                ) : (
                  <View style={!selectedSubject && styles.disabledUploader}>
                    <FileUploader
                      onUploadSuccess={handleUploadSuccess}
                      onUploadError={handleUploadError}
                      buttonTitle={selectedSubject ? "Select File" : "Select Subject First"}
                      apiUrl="https://dpsmushkipur.com/bine/api.php?task=upload"
                      maxSize={5 * 1024 * 1024}
                      allowedTypes={["jpg", "jpeg", "png", "gif", "pdf"]}
                      theme={{
                        primary: '#4a6ea9',
                        success: '#4CAF50',
                        error: '#F44336',
                        warning: '#FF9800',
                        background: '#FFFFFF',
                        text: '#333333',
                      }}
                      style={styles.fileUploader}
                    />
                  </View>
                )}
              </View>
              
              {/* Submit Button */}
              <TouchableOpacity
                style={[
                  styles.submitButton, 
                  (!selectedSubject || submitting) && styles.disabledButton
                ]}
                onPress={submitHomework}
                disabled={!selectedSubject || submitting}
                activeOpacity={0.8}
              >
                {submitting ? (
                  <ActivityIndicator size="small" color="#fff" />
                ) : (
                  <>
                    <Text style={styles.submitButtonText}>Submit Homework</Text>
                    <Ionicons name="send" size={18} color="#fff" style={{ marginLeft: 8 }} />
                  </>
                )}
              </TouchableOpacity>
            </Animated.View>

            {/* Success Message */}
            <SuccessMessage />

            {/* Modals */}
            <ClassSelectionModal />
            <SubjectSelectionModal />
          </ScrollView>
          ) : (
            renderHistoryTab()
          )}

          {/* Image Modal for attachments */}
          <Modal
            visible={imageModalVisible}
            transparent={true}
            animationType="fade"
            onRequestClose={() => setImageModalVisible(false)}
          >
            <View style={styles.imageModalContainer}>
              <TouchableOpacity 
                style={styles.imageModalCloseBtn}
                onPress={() => setImageModalVisible(false)}
              >
                <Ionicons name="close-circle" size={38} color="#fff" />
              </TouchableOpacity>
              
              <Image
                source={{ uri: selectedImage }}
                style={styles.imageModalPreview}
                resizeMode="contain"
              />
              
              <TouchableOpacity
                style={styles.imageModalDownloadBtn}
                onPress={() => {
                  Linking.openURL(selectedImage).catch(() => {
                    Alert.alert('Error', 'Cannot open browser link');
                  });
                }}
              >
                <LinearGradient
                  colors={["#4CAF50", "#1B5E20"]}
                  style={styles.imageModalDownloadGradient}
                >
                  <Ionicons name="download" size={18} color="#fff" />
                  <Text style={styles.imageModalDownloadText}>Open in Browser</Text>
                </LinearGradient>
              </TouchableOpacity>
            </View>
          </Modal>

        </KeyboardAvoidingView>
      </SafeAreaView>
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
    backgroundColor: '#f7f9fc',
  },
  header: {
    height: 100,
    overflow: "hidden",
  },
  headerGradient: {
    position: "absolute",
    left: 0,
    right: 0,
    top: 0,
    height: 70,
  },
  headerContent: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 20,
    paddingTop: 15,
  },
  backButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: "rgba(255, 255, 255, 0.2)",
    justifyContent: "center",
    alignItems: "center",
  },
  headerTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#ffffff",
  },
  infoButton: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: "rgba(255, 255, 255, 0.2)",
    justifyContent: "center",
    alignItems: "center",
  },
  container: {
    flex: 1,
  },
  contentContainer: {
    padding: 15,
  },
  card: {
    backgroundColor: 'white',
    borderRadius: 12,
    padding: 15,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.1,
    shadowRadius: 6,
    elevation: 3,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 20,
    paddingBottom: 15,
    borderBottomWidth: 1,
    borderBottomColor: '#f0f0f0',
  },
  cardHeaderText: {
    fontSize: 18,
    fontWeight: '600',
    color: '#4a6ea9',
    marginLeft: 8,
  },
  section: {
    marginBottom: 20,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '600',
    color: '#333',
    marginBottom: 6,
  },
  sectionSubtitle: {
    fontSize: 13,
    color: '#888',
    marginBottom: 10,
  },
  selector: {
    backgroundColor: 'white',
    borderRadius: 10,
    padding: 14,
    borderWidth: 1,
    borderColor: '#ddd',
    flexDirection: 'row',
    alignItems: 'center',
    elevation: 1,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  selectorIcon: {
    marginRight: 10,
  },
  selectorText: {
    fontSize: 15,
    color: '#333',
    flex: 1,
  },
  selectorPlaceholder: {
    color: '#999',
  },
  dateContainer: {
    backgroundColor: '#f5f8ff',
    borderRadius: 10,
    padding: 14,
    borderWidth: 1,
    borderColor: '#e0e7ff',
    flexDirection: 'row',
    alignItems: 'center',
  },
  dateText: {
    fontSize: 15,
    color: '#333',
    fontWeight: '500',
  },
  textInput: {
    backgroundColor: 'white',
    borderRadius: 10,
    padding: 14,
    borderWidth: 1,
    borderColor: '#ddd',
    textAlignVertical: 'top',
    fontSize: 15,
    minHeight: 150,
  },
  // File uploader styles
  fileUploader: {
    marginTop: 5,
  },
  disabledUploader: {
    opacity: 0.7,
    pointerEvents: 'none',
  },
  fileInfoDisplay: {
    flexDirection: 'row',
    backgroundColor: '#f5f8ff',
    borderRadius: 10,
    padding: 14,
    borderWidth: 1,
    borderColor: '#e0e7ff',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  fileInfoContent: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  pdfIconContainer: {
    width: 42,
    height: 42,
    backgroundColor: '#e74c3c',
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  imagePreview: {
    width: 42,
    height: 42,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#ddd',
  },
  fileDetails: {
    marginLeft: 14,
    flex: 1,
  },
  fileName: {
    fontSize: 14,
    color: '#333',
    fontWeight: '500',
  },
  fileMetaContainer: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginTop: 4,
  },
  fileSize: {
    fontSize: 12,
    color: '#666',
  },
  uploadedText: {
    fontSize: 12,
    color: '#4caf50',
    fontWeight: '500',
  },
  removeFileButton: {
    padding: 2,
  },
  submitButton: {
    backgroundColor: '#4a6ea9',
    borderRadius: 10,
    padding: 16,
    alignItems: 'center',
    flexDirection: 'row',
    justifyContent: 'center',
    marginTop: 10,
    elevation: 2,
    shadowColor: '#4a6ea9',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
  },
  submitButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: 'white',
  },
  disabledSection: {
    opacity: 0.7,
  },
  disabledSelector: {
    backgroundColor: '#f5f5f5',
    borderColor: '#e0e0e0',
  },
  disabledInput: {
    backgroundColor: '#f5f5f5',
    borderColor: '#e0e0e0',
  },
  disabledButton: {
    backgroundColor: '#a0a0a0',
    elevation: 0,
    shadowOpacity: 0,
  },
  
  // Success message
  successMessage: {
    marginTop: 20,
    backgroundColor: '#4caf50',
    borderRadius: 10,
    padding: 16,
    flexDirection: 'row',
    alignItems: 'center',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
  },
  successText: {
    color: 'white',
    fontSize: 16,
    fontWeight: '500',
    marginLeft: 10,
  },
  
  // Modal styles
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
  },
  modalContainer: {
    justifyContent: 'flex-end',
    height: '70%',
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
    backgroundColor: '#e0e0e0',
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
    backgroundColor: 'rgba(74, 110, 169, 0.08)',
  },
  classIconContainer: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: '#4a6ea9',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  subjectIconContainer: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: '#4caf50',
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: 14,
  },
  classIcon: {
    color: 'white',
    fontWeight: 'bold',
    fontSize: 16,
  },
  modalItemContent: {
    flex: 1,
  },
  modalItemText: {
    fontSize: 15,
    color: '#333',
    fontWeight: '500',
  },
  modalItemSubtext: {
    fontSize: 13,
    color: '#666',
    marginTop: 2,
  },
  modalSeparator: {
    height: 1,
    backgroundColor: '#f0f0f0',
    marginVertical: 2,
  },
  modalCloseButton: {
    marginTop: 15,
    padding: 14,
    backgroundColor: '#f5f5f5',
    borderRadius: 10,
    alignItems: 'center',
  },
  modalCloseButtonText: {
    fontSize: 16,
    color: '#555',
    fontWeight: '500',
  },
  loadingContainer: {
    padding: 30,
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 10,
    color: '#666',
    fontSize: 14,
  },
  noDataContainer: {
    padding: 40,
    alignItems: 'center',
  },
  noDataText: {
    marginTop: 10,
    color: '#777',
    fontSize: 16,
    textAlign: 'center',
  },
  // Tab styles
  tabsHeader: {
    flexDirection: 'row',
    backgroundColor: '#fff',
    padding: 8,
    marginHorizontal: 16,
    marginTop: 14,
    borderRadius: 14,
    gap: 8,
    elevation: 1,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
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
  // History tab styles
  loadingHistoryContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    paddingVertical: 80,
  },
  loadingHistoryText: {
    marginTop: 12,
    fontSize: 14,
    color: '#666',
  },
  historyListContent: {
    paddingHorizontal: 16,
    paddingTop: 14,
    paddingBottom: 30,
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
    fontSize: 16,
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
    paddingHorizontal: 12,
  },
  historyCard: {
    backgroundColor: '#fff',
    borderRadius: 16,
    padding: 16,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#EDF2F7',
    elevation: 1,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.02,
    shadowRadius: 3,
  },
  historyCardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    borderBottomWidth: 1,
    borderBottomColor: '#F7FAFC',
    paddingBottom: 10,
    marginBottom: 12,
  },
  historyHeaderLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  subjectIconCircle: {
    width: 36,
    height: 36,
    borderRadius: 18,
    alignItems: 'center',
    justifyContent: 'center',
  },
  historySubjectText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: '#2D3748',
  },
  historyClassText: {
    fontSize: 12,
    color: '#718096',
    marginTop: 2,
  },
  historyDateBadge: {
    backgroundColor: '#EDF2F7',
    paddingHorizontal: 8,
    paddingVertical: 4,
    borderRadius: 8,
  },
  historyDateText: {
    fontSize: 11,
    fontWeight: '600',
    color: '#4A5568',
  },
  historyCardBody: {
    gap: 8,
  },
  historyLabel: {
    fontSize: 12,
    fontWeight: '600',
    color: '#1B5E20',
  },
  historyDescriptionText: {
    fontSize: 13,
    color: '#4A5568',
    lineHeight: 18,
  },
  historyAttachmentBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F0FDF4',
    paddingHorizontal: 12,
    paddingVertical: 10,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#DCFCE7',
    marginTop: 6,
    gap: 8,
  },
  historyAttachmentText: {
    fontSize: 12,
    fontWeight: '500',
    color: '#1B5E20',
    maxWidth: '80%',
  },
  // Image modal styles
  imageModalContainer: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.95)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  imageModalCloseBtn: {
    position: 'absolute',
    top: 50,
    right: 20,
    zIndex: 10,
  },
  imageModalPreview: {
    width: width - 32,
    height: height - 200,
  },
  imageModalDownloadBtn: {
    position: 'absolute',
    bottom: 40,
    borderRadius: 25,
    overflow: 'hidden',
  },
  imageModalDownloadGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: 12,
    paddingHorizontal: 24,
    gap: 8,
  },
  imageModalDownloadText: {
    color: '#fff',
    fontSize: 14,
    fontWeight: 'bold',
  },
});