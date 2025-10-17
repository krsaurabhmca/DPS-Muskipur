import { Ionicons } from '@expo/vector-icons';
import DateTimePicker from '@react-native-community/datetimepicker';
import { useNavigation } from '@react-navigation/native';
import { format } from 'date-fns';
import { LinearGradient } from 'expo-linear-gradient';
import { StatusBar } from 'expo-status-bar';
import { useEffect, useState } from 'react';
import {
    ActivityIndicator,
    Alert,
    Animated,
    Dimensions,
    KeyboardAvoidingView,
    Platform,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View
} from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';

// Import the FileUploader component
import FileUploader from './FileUploader';

const { width } = Dimensions.get('window');

export default function NoticeScreen() {
  const navigation = useNavigation();
  const insets = useSafeAreaInsets();
  
  // Form state
  const [noticeTitle, setNoticeTitle] = useState('');
  const [noticeDetails, setNoticeDetails] = useState('');
  const [noticeDate, setNoticeDate] = useState(new Date());
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [uploadedFileInfo, setUploadedFileInfo] = useState(null);
  
  // UI state
  const [loading, setLoading] = useState(false);
  const [submitSuccess, setSubmitSuccess] = useState(false);
  const [errors, setErrors] = useState({});
  
  // Animation states
  const [fadeAnim] = useState(new Animated.Value(0));
  const [scaleAnim] = useState(new Animated.Value(0.95));

  // Start animation on component mount
  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 500,
        useNativeDriver: true,
      }),
      Animated.timing(scaleAnim, {
        toValue: 1,
        duration: 500,
        useNativeDriver: true,
      }),
    ]).start();
  }, []);

  // Handle date change
  const onDateChange = (event, selectedDate) => {
    const currentDate = selectedDate || noticeDate;
    setShowDatePicker(Platform.OS === 'ios');
    setNoticeDate(currentDate);
  };

  // Handle file upload success
  const handleUploadSuccess = (fileInfo) => {
    console.log('File uploaded successfully:', fileInfo);
    setUploadedFileInfo(fileInfo);
  };

  // Handle file upload error
  const handleUploadError = (error) => {
    Alert.alert('Upload Error', error);
    setUploadedFileInfo(null);
  };

  // Validate form
  const validateForm = () => {
    const newErrors = {};
    
    if (!noticeTitle.trim()) {
      newErrors.title = 'Notice title is required';
    }
    
    if (!noticeDetails.trim()) {
      newErrors.details = 'Notice details are required';
    }
    
    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };
  
  // Handle back navigation
  const handleBack = () => {
    // Navigate back or close the screen
    navigation.goBack();
  };

  // Submit notice
  const handleSubmit = async () => {
    if (!validateForm()) {
      return;
    }
    
    setLoading(true);
    
    try {
      const formattedDate = format(noticeDate, 'yyyy-MM-dd');
      const noticeData = {
        notice_date: formattedDate,
        notice_title: noticeTitle,
        notice_details: noticeDetails,
        notice_attachment: uploadedFileInfo ? uploadedFileInfo.file_name : ''
      };
      
      console.log('Submitting notice with data:', noticeData);
      
      const response = await fetch('https://dpsmushkipur.com/bine/api.php?task=notice', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(noticeData)
      });
      
      const result = await response.json();
      
      if (result.status === 'success') {
        // Show success state
        setSubmitSuccess(true);
        
        // Reset form after 2 seconds
        setTimeout(() => {
          setNoticeTitle('');
          setNoticeDetails('');
          setNoticeDate(new Date());
          setUploadedFileInfo(null);
          setSubmitSuccess(false);
        }, 2000);
        
        console.log('Notice created successfully:', result);
      } else {
        Alert.alert('Error', 'Failed to create notice');
      }
    } catch (error) {
      console.error('Error creating notice:', error);
      Alert.alert('Error', 'An unexpected error occurred');
    } finally {
      setLoading(false);
    }
  };

  // Show the file information
  const FileInfoDisplay = () => {
    if (!uploadedFileInfo) return null;
    
    const isPdf = uploadedFileInfo.file_type === 'pdf';
    
    return (
      <View style={styles.fileInfoDisplay}>
        <View style={styles.fileInfoContent}>
          <View style={[
            styles.fileIconContainer, 
            {backgroundColor: isPdf ? '#e74c3c' : '#3498db'}
          ]}>
            <Ionicons 
              name={isPdf ? "document-text" : "image"} 
              size={24} 
              color="white" 
            />
          </View>
          <View style={styles.fileDetails}>
            <Text style={styles.fileName} numberOfLines={1}>
              {uploadedFileInfo.file_name}
            </Text>
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
        >
          <Ionicons name="close-circle" size={22} color="#f44336" />
        </TouchableOpacity>
      </View>
    );
  };

  return (
    <SafeAreaView style={styles.container}>
      <StatusBar style="dark" />
      
      {/* Header with gradient background */}
      <LinearGradient
        colors={['#1e3c72', '#2a5298']}
        style={[styles.header, { paddingTop: insets.top > 0 ? 0 : 16 }]}
      >
        <View style={styles.headerContent}>
          <TouchableOpacity 
            onPress={handleBack}
            style={styles.backButton}
            hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
          >
            <Ionicons name="arrow-back" size={24} color="white" />
          </TouchableOpacity>
          
          <Text style={styles.headerTitle}>Create School Notice</Text>
          
          <View style={styles.headerRight}>
            <Ionicons name="notifications-outline" size={24} color="white" />
          </View>
        </View>
      </LinearGradient>
      
      <KeyboardAvoidingView
        behavior={Platform.OS === "ios" ? "padding" : "height"}
        style={styles.keyboardAvoidView}
        keyboardVerticalOffset={Platform.OS === "ios" ? 0 : 20}
      >
        <ScrollView 
          style={styles.scrollContainer}
          contentContainerStyle={styles.scrollContent}
          showsVerticalScrollIndicator={false}
        >
          <Animated.View 
            style={[
              styles.card,
              { 
                opacity: fadeAnim,
                transform: [{ scale: scaleAnim }] 
              }
            ]}
          >
            <View style={styles.cardHeader}>
              <View style={styles.iconBadge}>
                <Ionicons name="create-outline" size={22} color="white" />
              </View>
              <Text style={styles.cardTitle}>Notice Information</Text>
            </View>
            
            {/* Notice Date */}
            <View style={styles.formGroup}>
              <Text style={styles.formLabel}>Notice Date</Text>
              <TouchableOpacity 
                style={styles.dateSelector}
                onPress={() => setShowDatePicker(true)}
              >
                <Ionicons name="calendar-outline" size={22} color="#1e3c72" style={styles.inputIcon} />
                <Text style={styles.dateText}>
                  {format(noticeDate, 'dd MMMM yyyy')}
                </Text>
                <View style={styles.datePickerButton}>
                  <Ionicons name="chevron-down" size={20} color="#fff" />
                </View>
              </TouchableOpacity>
              
              {showDatePicker && (
                <DateTimePicker
                  value={noticeDate}
                  mode="date"
                  display={Platform.OS === 'ios' ? 'spinner' : 'default'}
                  onChange={onDateChange}
                />
              )}
            </View>
            
            {/* Notice Title */}
            <View style={styles.formGroup}>
              <Text style={styles.formLabel}>Notice Title</Text>
              <View style={[
                styles.inputContainer,
                errors.title ? styles.inputError : {}
              ]}>
                <Ionicons name="create-outline" size={22} color="#1e3c72" style={styles.inputIcon} />
                <TextInput
                  style={styles.input}
                  placeholder="Enter notice title"
                  value={noticeTitle}
                  onChangeText={setNoticeTitle}
                  maxLength={100}
                  placeholderTextColor="#888"
                />
              </View>
              {errors.title && <Text style={styles.errorText}>{errors.title}</Text>}
            </View>
            
            {/* Notice Details */}
            <View style={styles.formGroup}>
              <Text style={styles.formLabel}>Notice Details</Text>
              <View style={[
                styles.textAreaContainer,
                errors.details ? styles.inputError : {}
              ]}>
                <TextInput
                  style={styles.textArea}
                  placeholder="Enter notice details"
                  value={noticeDetails}
                  onChangeText={setNoticeDetails}
                  multiline
                  numberOfLines={6}
                  textAlignVertical="top"
                  placeholderTextColor="#888"
                />
              </View>
              {errors.details && <Text style={styles.errorText}>{errors.details}</Text>}
            </View>
            
            {/* Attachment */}
            <View style={styles.formGroup}>
              <Text style={styles.formLabel}>Attachment (Optional)</Text>
              <Text style={styles.formLabelHint}>PDF or Image up to 5MB</Text>
              
              {uploadedFileInfo ? (
                <FileInfoDisplay />
              ) : (
                <View style={styles.uploaderContainer}>
                  <FileUploader
                    onUploadSuccess={handleUploadSuccess}
                    onUploadError={handleUploadError}
                    buttonTitle="Upload Attachment"
                    apiUrl="https://dpsmushkipur.com/bine/api.php?task=upload"
                    maxSize={5 * 1024 * 1024}
                    allowedTypes={["jpg", "jpeg", "png", "gif", "pdf"]}
                    theme={{
                      primary: '#1e3c72',
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
                submitSuccess ? styles.successButton : {},
                loading ? styles.disabledButton : {}
              ]}
              onPress={handleSubmit}
              disabled={loading || submitSuccess}
              activeOpacity={0.8}
            >
              {loading ? (
                <ActivityIndicator color="#fff" size="small" />
              ) : submitSuccess ? (
                <View style={styles.successContainer}>
                  <Ionicons name="checkmark-circle" size={20} color="#fff" />
                  <Text style={styles.submitButtonText}>Notice Published!</Text>
                </View>
              ) : (
                <LinearGradient
                  colors={['#1e3c72', '#2a5298']}
                  style={styles.buttonGradient}
                >
                  <Text style={styles.submitButtonText}>Publish Notice</Text>
                  <Ionicons name="paper-plane" size={18} color="#fff" style={styles.submitIcon} />
                </LinearGradient>
              )}
            </TouchableOpacity>
          </Animated.View>
        </ScrollView>
      </KeyboardAvoidingView>
      
      {/* Success feedback overlay */}
      {submitSuccess && (
        <Animated.View 
          style={[
            styles.successOverlay,
            { opacity: fadeAnim }
          ]}
        >
          <View style={styles.successPopup}>
            <Ionicons name="checkmark-circle" size={60} color="#4CAF50" />
            <Text style={styles.successPopupText}>Notice Published Successfully!</Text>
          </View>
        </Animated.View>
      )}
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#f5f7fa',
  },
  keyboardAvoidView: {
    flex: 1,
  },
  header: {
    paddingBottom: 16,
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 2,
  },
  headerContent: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
    height: 45,
    paddingTop: 8,
  },
  backButton: {
    padding: 8,
    borderRadius: 20,
  },
  headerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: 'white',
  },
  headerRight: {
    width: 40,
    alignItems: 'center',
  },
  scrollContainer: {
    flex: 1,
  },
  scrollContent: {
    padding: 16,
    paddingBottom: 30,
  },
  card: {
    backgroundColor: 'white',
    borderRadius: 12,
    padding: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 5 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 5,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 25,
  },
  iconBadge: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: '#1e3c72',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
    elevation: 2,
    shadowColor: '#1e3c72',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 2,
  },
  cardTitle: {
    fontSize: 20,
    fontWeight: 'bold',
    color: '#2c3e50',
  },
  formGroup: {
    marginBottom: 20,
  },
  formLabel: {
    fontSize: 16,
    fontWeight: '600',
    color: '#2c3e50',
    marginBottom: 8,
  },
  formLabelHint: {
    fontSize: 13,
    color: '#7f8c8d',
    marginBottom: 10,
    marginTop: -4,
  },
  dateSelector: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'white',
    borderWidth: 1,
    borderColor: '#dce0e3',
    borderRadius: 10,
    height: 55,
    paddingLeft: 16,
  },
  dateText: {
    flex: 1,
    fontSize: 15,
    color: '#34495e',
    paddingVertical: 10,
  },
  datePickerButton: {
    backgroundColor: '#1e3c72',
    height: 53,
    width: 48,
    alignItems: 'center',
    justifyContent: 'center',
    borderTopRightRadius: 9,
    borderBottomRightRadius: 9,
  },
  inputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#dce0e3',
    borderRadius: 10,
    height: 55,
    paddingHorizontal: 16,
    backgroundColor: 'white',
  },
  inputError: {
    borderColor: '#F44336',
    borderWidth: 1.5,
  },
  inputIcon: {
    marginRight: 12,
  },
  input: {
    flex: 1,
    height: '100%',
    fontSize: 15,
    color: '#34495e',
  },
  textAreaContainer: {
    borderWidth: 1,
    borderColor: '#dce0e3',
    borderRadius: 10,
    backgroundColor: 'white',
    padding: 16,
  },
  textArea: {
    fontSize: 15,
    color: '#34495e',
    minHeight: 120,
  },
  errorText: {
    color: '#F44336',
    fontSize: 12,
    marginTop: 5,
    marginLeft: 5,
    fontWeight: '500',
  },
  uploaderContainer: {
    borderWidth: 1,
    borderColor: '#dce0e3',
    borderRadius: 10,
    padding: 16,
    backgroundColor: '#f9fafc',
    borderStyle: 'dashed',
  },
  fileUploader: {
    marginTop: 0,
  },
  fileInfoDisplay: {
    flexDirection: 'row',
    backgroundColor: '#f5f8ff',
    borderRadius: 10,
    padding: 16,
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
  fileIconContainer: {
    width: 42,
    height: 42,
    borderRadius: 8,
    alignItems: 'center',
    justifyContent: 'center',
  },
  fileDetails: {
    marginLeft: 14,
    flex: 1,
  },
  fileName: {
    fontSize: 14,
    color: '#34495e',
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
    color: '#7f8c8d',
  },
  uploadedText: {
    fontSize: 12,
    color: '#4caf50',
    fontWeight: '500',
  },
  removeFileButton: {
    padding: 5,
  },
  submitButton: {
    borderRadius: 10,
    height: 55,
    overflow: 'hidden',
    marginTop: 10,
  },
  buttonGradient: {
    flex: 1,
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 20,
  },
  disabledButton: {
    backgroundColor: '#a0a0a0',
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 20,
  },
  successButton: {
    backgroundColor: '#4CAF50',
    flexDirection: 'row',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 20,
  },
  successContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  submitButtonText: {
    color: 'white',
    fontSize: 16,
    fontWeight: 'bold',
  },
  submitIcon: {
    marginLeft: 8,
  },
  successOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(0,0,0,0.7)',
    justifyContent: 'center',
    alignItems: 'center',
    zIndex: 100,
  },
  successPopup: {
    backgroundColor: 'white',
    borderRadius: 16,
    padding: 30,
    alignItems: 'center',
    width: width * 0.8,
  },
  successPopupText: {
    fontSize: 18,
    fontWeight: 'bold',
    color: '#2c3e50',
    marginTop: 15,
    textAlign: 'center',
  },
});