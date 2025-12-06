import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import DateTimePicker from '@react-native-community/datetimepicker';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
  Alert,
  Dimensions,
  Image,
  Linking,
  Modal,
  Platform,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TouchableOpacity,
  View
} from 'react-native';
import RenderHtml from 'react-native-render-html';

const { width, height } = Dimensions.get('window');

const COLORS = {
  primary: '#1B5E20',
  secondary: '#4CAF50',
  accent: '#FFC107',
  white: '#FFFFFF',
  gray: '#757575',
  lightGray: '#E0E0E0',
  error: '#F44336',
  background: '#F5F7FA',
};

const SUBJECT_COLORS = [
  '#FF6B6B',
  '#4ECDC4',
  '#45B7D1',
  '#FFA07A',
  '#98D8C8',
  '#F7DC6F',
  '#BB8FCE',
  '#85C1E2',
];

export default function HomeworkScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [homework, setHomework] = useState([]);
  const [selectedDate, setSelectedDate] = useState(new Date().toISOString().split('T')[0]);
  const [error, setError] = useState('');
  const [imageModalVisible, setImageModalVisible] = useState(false);
  const [selectedImage, setSelectedImage] = useState(null);
  const [showDatePicker, setShowDatePicker] = useState(false);
  const [tempDate, setTempDate] = useState(new Date());

  useEffect(() => {
    fetchHomework();
  }, [selectedDate]);

  const fetchHomework = async () => {
    try {
      setLoading(true);
      setError('');

      const studentId = await AsyncStorage.getItem('student_id');
      
      if (!studentId) {
        router.replace('/index');
        return;
      }

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=get_homework',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ 
            student_id: studentId,
            hw_date: selectedDate 
          }),
        }
      );

      const data = await response.json();
      
      if (data && Array.isArray(data) && data.length > 0) {
        setHomework(data);
        
        // Cache homework
        await AsyncStorage.setItem(`homework_${selectedDate}`, JSON.stringify(data));
      } else {
        setHomework([]);
        setError('No homework found for this date');
      }
    } catch (err) {
      console.error('Error fetching homework:', err);
      setError('Failed to load homework');
      
      // Try to load cached homework
      try {
        const cachedHomework = await AsyncStorage.getItem(`homework_${selectedDate}`);
        if (cachedHomework) {
          setHomework(JSON.parse(cachedHomework));
          setError('Showing cached homework. Network error occurred.');
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
    fetchHomework();
  };

  const handleDateChange = (days) => {
    const currentDate = new Date(selectedDate);
    currentDate.setDate(currentDate.getDate() + days);
    setSelectedDate(currentDate.toISOString().split('T')[0]);
  };

  const handleDatePickerChange = (event, date) => {
    if (Platform.OS === 'android') {
      setShowDatePicker(false);
    }
    
    if (event.type === 'set' && date) {
      setTempDate(date);
      if (Platform.OS === 'android') {
        setSelectedDate(date.toISOString().split('T')[0]);
      }
    }
  };

  const handleDatePickerConfirm = () => {
    setSelectedDate(tempDate.toISOString().split('T')[0]);
    setShowDatePicker(false);
  };

  const handleDatePickerCancel = () => {
    setShowDatePicker(false);
    setTempDate(new Date(selectedDate));
  };

  const openDatePicker = () => {
    setTempDate(new Date(selectedDate));
    setShowDatePicker(true);
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);

    if (date.toDateString() === today.toDateString()) {
      return 'Today';
    } else if (date.toDateString() === yesterday.toDateString()) {
      return 'Yesterday';
    } else if (date.toDateString() === tomorrow.toDateString()) {
      return 'Tomorrow';
    } else {
      const options = { day: '2-digit', month: 'short', year: 'numeric' };
      return date.toLocaleDateString('en-GB', options);
    }
  };

  const getFileExtension = (filename) => {
    if (!filename) return '';
    return filename.split('.').pop().toLowerCase();
  };

  const isImageFile = (filename) => {
    const ext = getFileExtension(filename);
    return ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'].includes(ext);
  };

  const isPdfFile = (filename) => {
    const ext = getFileExtension(filename);
    return ext === 'pdf';
  };

  const handleFileOpen = (filename) => {
    if (!filename) return;
    const fileUrl = `https://dpsmushkipur.com/bine/homework/${filename}`;

    if (isImageFile(filename)) {
      // Open image in modal
      setSelectedImage(fileUrl);
      setImageModalVisible(true);
    } else if (isPdfFile(filename)) {
      // Open PDF in external viewer
      Linking.openURL(fileUrl).catch(err => {
        Alert.alert('Error', 'Cannot open PDF file');
        console.error('Error opening PDF:', err);
      });
    } else {
      // Open other files in browser
      Linking.openURL(fileUrl).catch(err => {
        Alert.alert('Error', 'Cannot open file');
        console.error('Error opening file:', err);
      });
    }
  };

  const getSubjectColor = (subjectId) => {
    const index = parseInt(subjectId) % SUBJECT_COLORS.length;
    return SUBJECT_COLORS[index];
  };

  if (loading && !refreshing) {
    return (
      <View style={styles.loadingContainer}>
        <View style={styles.logoCircle}>
          <Ionicons name="book" size={40} color={COLORS.primary} />
        </View>
        <Text style={styles.loadingText}>Loading homework...</Text>
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
          <Text style={styles.headerTitle}>Homework</Text>
          <View style={{ width: 40 }} />
        </View>

        {/* Stats */}
        <View style={styles.statsContainer}>
          <View style={styles.statItem}>
            <Ionicons name="calendar" size={24} color={COLORS.white} />
            <Text style={styles.statLabel}>Selected Date</Text>
            <Text style={styles.statValue}>{formatDate(selectedDate)}</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statItem}>
            <Ionicons name="clipboard" size={24} color={COLORS.white} />
            <Text style={styles.statLabel}>Assignments</Text>
            <Text style={styles.statValue}>{homework.length}</Text>
          </View>
        </View>

        {/* Date Selector */}
        <View style={styles.dateSelector}>
          <TouchableOpacity
            style={styles.dateButton}
            onPress={() => handleDateChange(-1)}
          >
            <Ionicons name="chevron-back" size={24} color={COLORS.white} />
          </TouchableOpacity>
          
          <TouchableOpacity 
            style={styles.dateCenterButton}
            onPress={openDatePicker}
          >
            <Ionicons name="calendar-outline" size={18} color={COLORS.primary} />
            <Text style={styles.dateText}>{formatDate(selectedDate)}</Text>
          </TouchableOpacity>
          
          <TouchableOpacity
            style={styles.dateButton}
            onPress={() => handleDateChange(1)}
          >
            <Ionicons name="chevron-forward" size={24} color={COLORS.white} />
          </TouchableOpacity>
        </View>
      </LinearGradient>

      {/* Error Banner */}
      {error && (
        <View style={styles.errorBanner}>
          <Ionicons name="information-circle" size={16} color={COLORS.error} />
          <Text style={styles.errorBannerText}>{error}</Text>
        </View>
      )}

      {/* Homework List */}
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
        {homework.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="checkmark-done-circle" size={80} color={COLORS.lightGray} />
            <Text style={styles.emptyText}>No homework for this date</Text>
            <Text style={styles.emptySubText}>Enjoy your free time! 🎉</Text>
          </View>
        ) : (
          homework.map((item, index) => (
            <HomeworkCard
              key={item.id}
              homework={item}
              index={index}
              getSubjectColor={getSubjectColor}
              handleFileOpen={handleFileOpen}
              isImageFile={isImageFile}
              isPdfFile={isPdfFile}
            />
          ))
        )}
      </ScrollView>

      {/* Image Modal */}
      <Modal
        visible={imageModalVisible}
        transparent={true}
        animationType="fade"
        onRequestClose={() => setImageModalVisible(false)}
      >
        <View style={styles.modalContainer}>
          <TouchableOpacity 
            style={styles.modalCloseButton}
            onPress={() => setImageModalVisible(false)}
          >
            <Ionicons name="close-circle" size={40} color={COLORS.white} />
          </TouchableOpacity>
          
          <Image
            source={{ uri: selectedImage }}
            style={styles.modalImage}
            resizeMode="contain"
          />
          
          <TouchableOpacity
            style={styles.downloadButton}
            onPress={() => {
              Linking.openURL(selectedImage);
            }}
          >
            <LinearGradient
              colors={[COLORS.secondary, COLORS.primary]}
              style={styles.downloadGradient}
            >
              <Ionicons name="download" size={20} color={COLORS.white} />
              <Text style={styles.downloadText}>Download Image</Text>
            </LinearGradient>
          </TouchableOpacity>
        </View>
      </Modal>

      {/* Date Picker */}
      {showDatePicker && (
        <>
          {Platform.OS === 'ios' ? (
            <Modal
              transparent={true}
              animationType="slide"
              visible={showDatePicker}
              onRequestClose={handleDatePickerCancel}
            >
              <View style={styles.datePickerModalContainer}>
                <TouchableOpacity 
                  style={styles.datePickerBackdrop}
                  activeOpacity={1}
                  onPress={handleDatePickerCancel}
                />
                <View style={styles.datePickerModal}>
                  <View style={styles.datePickerHeader}>
                    <TouchableOpacity onPress={handleDatePickerCancel}>
                      <Text style={styles.datePickerCancelText}>Cancel</Text>
                    </TouchableOpacity>
                    <Text style={styles.datePickerTitle}>Select Date</Text>
                    <TouchableOpacity onPress={handleDatePickerConfirm}>
                      <Text style={styles.datePickerConfirmText}>Done</Text>
                    </TouchableOpacity>
                  </View>
                  <DateTimePicker
                    value={tempDate}
                    mode="date"
                    display="spinner"
                    onChange={handleDatePickerChange}
                    textColor={COLORS.primary}
                  />
                </View>
              </View>
            </Modal>
          ) : (
            <DateTimePicker
              value={tempDate}
              mode="date"
              display="default"
              onChange={handleDatePickerChange}
            />
          )}
        </>
      )}
    </View>
  );
}

function HomeworkCard({ homework, index, getSubjectColor, handleFileOpen, isImageFile, isPdfFile }) {
  const [expanded, setExpanded] = useState(false);
  const subjectColor = getSubjectColor(homework.subject_id);

  const getFileIcon = (filename) => {
    if (isImageFile(filename)) return 'image';
    if (isPdfFile(filename)) return 'document-text';
    return 'document-attach';
  };

  const getFileType = (filename) => {
    if (isImageFile(filename)) return 'Image';
    if (isPdfFile(filename)) return 'PDF Document';
    return 'Document';
  };

  const htmlConfig = {
    width: width - 80,
  };

  const tagsStyles = {
    body: {
      color: COLORS.gray,
      fontSize: 14,
      lineHeight: 22,
    },
  };

  return (
    <View style={styles.homeworkCard}>
      {/* Subject Badge */}
      <View style={[styles.subjectBadge, { backgroundColor: subjectColor }]}>
        <Text style={styles.subjectBadgeText}>Subject #{homework.subject_id}</Text>
      </View>

      {/* Card Content */}
      <TouchableOpacity
        activeOpacity={0.7}
        onPress={() => setExpanded(!expanded)}
      >
        <View style={styles.cardHeader}>
          <View style={[styles.subjectIcon, { backgroundColor: subjectColor + '20' }]}>
            <Ionicons name="book" size={24} color={subjectColor} />
          </View>
          
          <View style={styles.classInfo}>
            <Text style={styles.classText}>
              Class {homework.hw_class} - {homework.hw_section}
            </Text>
            <Text style={styles.dateText}>
              {new Date(homework.hw_date).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
              })}
            </Text>
          </View>

          {homework.hw_file && (
            <View style={styles.attachmentIndicator}>
              <Ionicons name="attach" size={20} color={subjectColor} />
            </View>
          )}
        </View>

        {/* Homework Text */}
        <View style={styles.homeworkContent}>
          <Text style={styles.homeworkLabel}>Assignment:</Text>
          <View style={styles.homeworkTextContainer}>
            <RenderHtml
              contentWidth={htmlConfig.width}
              source={{ html: homework.hw_text }}
              tagsStyles={tagsStyles}
            />
          </View>
        </View>

        {/* Attachment Section */}
        {homework.hw_file && (
          <View style={styles.attachmentSection}>
            <TouchableOpacity
              style={[styles.attachmentButton, { borderColor: subjectColor }]}
              onPress={() => handleFileOpen(homework.hw_file)}
            >
              <View style={[styles.attachmentIcon, { backgroundColor: subjectColor + '20' }]}>
                <Ionicons 
                  name={getFileIcon(homework.hw_file)} 
                  size={24} 
                  color={subjectColor} 
                />
              </View>
              <View style={styles.attachmentInfo}>
                <Text style={styles.attachmentName} numberOfLines={1}>
                  {homework.hw_file}
                </Text>
                <Text style={[styles.attachmentType, { color: subjectColor }]}>
                  {getFileType(homework.hw_file)}
                </Text>
              </View>
              <Ionicons name="chevron-forward" size={20} color={subjectColor} />
            </TouchableOpacity>
          </View>
        )}
      </TouchableOpacity>
    </View>
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
    paddingBottom: 20,
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
  statsContainer: {
    flexDirection: 'row',
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    marginHorizontal: 20,
    marginBottom: 20,
    borderRadius: 15,
    padding: 15,
  },
  statItem: {
    flex: 1,
    alignItems: 'center',
    gap: 4,
  },
  statDivider: {
    width: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
  },
  statLabel: {
    fontSize: 11,
    color: COLORS.white,
    opacity: 0.9,
  },
  statValue: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  dateSelector: {
    flexDirection: 'row',
    alignItems: 'center',
    marginHorizontal: 20,
    gap: 10,
  },
  dateButton: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  dateCenterButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: COLORS.white,
    paddingVertical: 12,
    borderRadius: 22,
    gap: 8,
  },
  dateText: {
    fontSize: 15,
    fontWeight: 'bold',
    color: COLORS.primary,
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
    paddingHorizontal: 20,
    paddingTop: 20,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyText: {
    fontSize: 18,
    fontWeight: '600',
    color: COLORS.gray,
    marginTop: 20,
  },
  emptySubText: {
    fontSize: 14,
    color: COLORS.gray,
    marginTop: 8,
  },
  homeworkCard: {
    backgroundColor: COLORS.white,
    borderRadius: 20,
    marginBottom: 15,
    overflow: 'hidden',
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
  },
  subjectBadge: {
    paddingHorizontal: 15,
    paddingVertical: 8,
  },
  subjectBadgeText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.white,
    textTransform: 'uppercase',
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 15,
    paddingTop: 10,
  },
  subjectIcon: {
    width: 48,
    height: 48,
    borderRadius: 24,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  classInfo: {
    flex: 1,
  },
  classText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 4,
  },
  attachmentIndicator: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: COLORS.accent + '20',
    justifyContent: 'center',
    alignItems: 'center',
  },
  homeworkContent: {
    paddingHorizontal: 15,
    paddingBottom: 15,
  },
  homeworkLabel: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 8,
  },
  homeworkTextContainer: {
    backgroundColor: COLORS.background,
    padding: 12,
    borderRadius: 10,
  },
  attachmentSection: {
    paddingHorizontal: 15,
    paddingBottom: 15,
  },
  attachmentButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.background,
    borderRadius: 12,
    padding: 12,
    borderWidth: 1,
  },
  attachmentIcon: {
    width: 48,
    height: 48,
    borderRadius: 24,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  attachmentInfo: {
    flex: 1,
  },
  attachmentName: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 4,
  },
  attachmentType: {
    fontSize: 12,
    fontWeight: '500',
  },
  modalContainer: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.95)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  modalCloseButton: {
    position: 'absolute',
    top: 50,
    right: 20,
    zIndex: 10,
  },
  modalImage: {
    width: width - 40,
    height: height - 200,
  },
  downloadButton: {
    position: 'absolute',
    bottom: 40,
    borderRadius: 25,
    overflow: 'hidden',
    elevation: 5,
  },
  downloadGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 30,
    paddingVertical: 15,
    gap: 10,
  },
  downloadText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  datePickerModalContainer: {
    flex: 1,
    justifyContent: 'flex-end',
  },
  datePickerBackdrop: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
  },
  datePickerModal: {
    backgroundColor: COLORS.white,
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    paddingBottom: 20,
  },
  datePickerHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 15,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.lightGray,
  },
  datePickerTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  datePickerCancelText: {
    fontSize: 16,
    color: COLORS.error,
  },
  datePickerConfirmText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.secondary,
  },
});