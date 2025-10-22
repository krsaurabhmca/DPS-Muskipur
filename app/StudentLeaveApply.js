import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import DateTimePicker from '@react-native-community/datetimepicker';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useState } from 'react';
import {
    ActivityIndicator,
    Alert,
    KeyboardAvoidingView,
    Platform,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
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
};

export default function LeaveApplyScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    from_date: new Date(),
    to_date: new Date(),
    cause: '',
  });
  const [showFromDatePicker, setShowFromDatePicker] = useState(false);
  const [showToDatePicker, setShowToDatePicker] = useState(false);
  const [errors, setErrors] = useState({});

  const validateForm = () => {
    const newErrors = {};

    if (!formData.from_date) {
      newErrors.from_date = 'Start date is required';
    }

    if (!formData.to_date) {
      newErrors.to_date = 'End date is required';
    }

    if (formData.from_date && formData.to_date) {
      if (formData.to_date < formData.from_date) {
        newErrors.to_date = 'End date must be after start date';
      }
    }

    if (!formData.cause.trim()) {
      newErrors.cause = 'Reason for leave is required';
    } else if (formData.cause.trim().length < 10) {
      newErrors.cause = 'Reason must be at least 10 characters';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleSubmit = async () => {
    if (!validateForm()) {
      return;
    }

    try {
      setLoading(true);

      const studentId = await AsyncStorage.getItem('student_id');
      
      if (!studentId) {
        Alert.alert('Error', 'Student ID not found. Please login again.');
        router.replace('/index');
        return;
      }

      const payload = {
        student_id: studentId,
        from_date: formatDateForAPI(formData.from_date),
        to_date: formatDateForAPI(formData.to_date),
        cause: formData.cause.trim(),
      };

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=student_leave_apply',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(payload),
        }
      );

      const data = await response.json();

      if (data.status === 'success') {
        Alert.alert(
          'Success! ✅',
          'Leave applied successfully. Your request will be reviewed soon.',
          [
            {
              text: 'OK',
              onPress: () => {
                // Reset form
                setFormData({
                  from_date: new Date(),
                  to_date: new Date(),
                  cause: '',
                });
                setErrors({});
                // Optionally go back
                router.back();
              },
            },
          ]
        );
      } else {
        Alert.alert('Error', data.msg || 'Failed to apply for leave');
      }
    } catch (err) {
      console.error('Error applying for leave:', err);
      Alert.alert('Error', 'Network error. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const formatDateForAPI = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  };

  const formatDateForDisplay = (date) => {
    const options = { day: '2-digit', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('en-GB', options);
  };

  const handleFromDateChange = (event, selectedDate) => {
    setShowFromDatePicker(false);
    if (selectedDate) {
      setFormData({ ...formData, from_date: selectedDate });
      setErrors({ ...errors, from_date: null });
      
      // Auto-adjust to_date if it's before from_date
      if (formData.to_date < selectedDate) {
        setFormData({ 
          ...formData, 
          from_date: selectedDate, 
          to_date: selectedDate 
        });
      }
    }
  };

  const handleToDateChange = (event, selectedDate) => {
    setShowToDatePicker(false);
    if (selectedDate) {
      setFormData({ ...formData, to_date: selectedDate });
      setErrors({ ...errors, to_date: null });
    }
  };

  const calculateDays = () => {
    const diffTime = Math.abs(formData.to_date - formData.from_date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    return diffDays;
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : 'height'}
    >
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
          <Text style={styles.headerTitle}>Apply for Leave</Text>
          <View style={{ width: 40 }} />
        </View>

        {/* Info Card */}
        <View style={styles.infoCard}>
          <View style={styles.infoIconContainer}>
            <Ionicons name="calendar-outline" size={32} color={COLORS.secondary} />
          </View>
          <View style={styles.infoTextContainer}>
            <Text style={styles.infoText}>
              Submit your leave application and we'll review it promptly
            </Text>
            {calculateDays() > 0 && (
              <View style={styles.daysCounter}>
                <Ionicons name="time-outline" size={16} color={COLORS.accent} />
                <Text style={styles.daysCounterText}>
                  {calculateDays()} {calculateDays() === 1 ? 'Day' : 'Days'}
                </Text>
              </View>
            )}
          </View>
        </View>
      </LinearGradient>

      <ScrollView
        style={styles.scrollView}
        showsVerticalScrollIndicator={false}
        keyboardShouldPersistTaps="handled"
      >
        <View style={styles.formContainer}>
          {/* From Date */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>
              <Ionicons name="calendar" size={16} color={COLORS.primary} /> From Date
            </Text>
            <TouchableOpacity
              style={[styles.dateInput, errors.from_date && styles.inputError]}
              onPress={() => setShowFromDatePicker(true)}
            >
              <View style={styles.dateContent}>
                <Ionicons name="calendar-outline" size={20} color={COLORS.secondary} />
                <Text style={styles.dateText}>
                  {formatDateForDisplay(formData.from_date)}
                </Text>
              </View>
              <Ionicons name="chevron-down" size={20} color={COLORS.gray} />
            </TouchableOpacity>
            {errors.from_date && (
              <Text style={styles.errorText}>{errors.from_date}</Text>
            )}
          </View>

          {/* To Date */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>
              <Ionicons name="calendar" size={16} color={COLORS.primary} /> To Date
            </Text>
            <TouchableOpacity
              style={[styles.dateInput, errors.to_date && styles.inputError]}
              onPress={() => setShowToDatePicker(true)}
            >
              <View style={styles.dateContent}>
                <Ionicons name="calendar-outline" size={20} color={COLORS.secondary} />
                <Text style={styles.dateText}>
                  {formatDateForDisplay(formData.to_date)}
                </Text>
              </View>
              <Ionicons name="chevron-down" size={20} color={COLORS.gray} />
            </TouchableOpacity>
            {errors.to_date && (
              <Text style={styles.errorText}>{errors.to_date}</Text>
            )}
          </View>

          {/* Duration Display */}
          {!errors.to_date && !errors.from_date && (
            <View style={styles.durationCard}>
              <LinearGradient
                colors={[COLORS.accent, '#FFD54F']}
                style={styles.durationGradient}
              >
                <Ionicons name="time" size={24} color={COLORS.primary} />
                <View style={styles.durationContent}>
                  <Text style={styles.durationValue}>{calculateDays()}</Text>
                  <Text style={styles.durationLabel}>
                    {calculateDays() === 1 ? 'Day' : 'Days'} Leave
                  </Text>
                </View>
              </LinearGradient>
            </View>
          )}

          {/* Reason/Cause */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>
              <Ionicons name="document-text" size={16} color={COLORS.primary} /> Reason for Leave
            </Text>
            <View style={[styles.textAreaContainer, errors.cause && styles.inputError]}>
              <TextInput
                style={styles.textArea}
                placeholder="Please explain the reason for your leave application..."
                placeholderTextColor={COLORS.gray}
                multiline
                numberOfLines={6}
                textAlignVertical="top"
                value={formData.cause}
                onChangeText={(text) => {
                  setFormData({ ...formData, cause: text });
                  setErrors({ ...errors, cause: null });
                }}
              />
              <View style={styles.textAreaFooter}>
                <Text style={styles.charCount}>
                  {formData.cause.length} characters
                </Text>
                {formData.cause.length >= 10 && (
                  <View style={styles.validIndicator}>
                    <Ionicons name="checkmark-circle" size={16} color={COLORS.success} />
                    <Text style={styles.validText}>Valid</Text>
                  </View>
                )}
              </View>
            </View>
            {errors.cause && (
              <Text style={styles.errorText}>{errors.cause}</Text>
            )}
          </View>

          {/* Submit Button */}
          <TouchableOpacity
            style={[styles.submitButton, loading && styles.submitButtonDisabled]}
            onPress={handleSubmit}
            disabled={loading}
          >
            <LinearGradient
              colors={loading ? [COLORS.gray, COLORS.gray] : [COLORS.secondary, COLORS.primary]}
              style={styles.submitGradient}
              start={{ x: 0, y: 0 }}
              end={{ x: 1, y: 0 }}
            >
              {loading ? (
                <>
                  <ActivityIndicator size="small" color={COLORS.white} />
                  <Text style={styles.submitButtonText}>Submitting...</Text>
                </>
              ) : (
                <>
                  <Ionicons name="paper-plane" size={20} color={COLORS.white} />
                  <Text style={styles.submitButtonText}>Submit Leave Application</Text>
                </>
              )}
            </LinearGradient>
          </TouchableOpacity>

          {/* Important Notes */}
          <View style={styles.notesCard}>
            <View style={styles.notesHeader}>
              <Ionicons name="information-circle" size={22} color={COLORS.accent} />
              <Text style={styles.notesTitle}>Important Notes</Text>
            </View>
            <View style={styles.noteItem}>
              <Ionicons name="ellipse" size={8} color={COLORS.gray} />
              <Text style={styles.noteText}>
                Leave applications will be reviewed within 24-48 hours
              </Text>
            </View>
            <View style={styles.noteItem}>
              <Ionicons name="ellipse" size={8} color={COLORS.gray} />
              <Text style={styles.noteText}>
                Ensure you provide a valid reason for your leave
              </Text>
            </View>
            <View style={styles.noteItem}>
              <Ionicons name="ellipse" size={8} color={COLORS.gray} />
              <Text style={styles.noteText}>
                You'll be notified once your application is approved
              </Text>
            </View>
            <View style={styles.noteItem}>
              <Ionicons name="ellipse" size={8} color={COLORS.gray} />
              <Text style={styles.noteText}>
                Contact admin for urgent leave requests
              </Text>
            </View>
          </View>
        </View>
      </ScrollView>

      {/* Date Pickers */}
      {showFromDatePicker && (
        <DateTimePicker
          value={formData.from_date}
          mode="date"
          display={Platform.OS === 'ios' ? 'spinner' : 'default'}
          onChange={handleFromDateChange}
          minimumDate={new Date()}
        />
      )}

      {showToDatePicker && (
        <DateTimePicker
          value={formData.to_date}
          mode="date"
          display={Platform.OS === 'ios' ? 'spinner' : 'default'}
          onChange={handleToDateChange}
          minimumDate={formData.from_date}
        />
      )}
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: COLORS.background,
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
  infoCard: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
    marginHorizontal: 20,
    padding: 15,
    borderRadius: 15,
    gap: 15,
  },
  infoIconContainer: {
    width: 50,
    height: 50,
    borderRadius: 25,
    backgroundColor: COLORS.secondary + '20',
    justifyContent: 'center',
    alignItems: 'center',
  },
  infoTextContainer: {
    flex: 1,
  },
  infoText: {
    fontSize: 13,
    color: COLORS.gray,
    lineHeight: 18,
  },
  daysCounter: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 8,
    gap: 6,
  },
  daysCounterText: {
    fontSize: 14,
    fontWeight: 'bold',
    color: COLORS.accent,
  },
  scrollView: {
    flex: 1,
  },
  formContainer: {
    padding: 20,
  },
  inputGroup: {
    marginBottom: 25,
  },
  label: {
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.primary,
    marginBottom: 10,
  },
  dateInput: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 16,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  dateContent: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 12,
  },
  dateText: {
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.primary,
  },
  durationCard: {
    marginBottom: 25,
    borderRadius: 15,
    overflow: 'hidden',
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.15,
    shadowRadius: 3,
  },
  durationGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: 20,
    gap: 15,
  },
  durationContent: {
    flex: 1,
  },
  durationValue: {
    fontSize: 28,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  durationLabel: {
    fontSize: 14,
    color: COLORS.primary,
    marginTop: 2,
  },
  textAreaContainer: {
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 15,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  textArea: {
    fontSize: 15,
    color: COLORS.primary,
    minHeight: 120,
    textAlignVertical: 'top',
  },
  textAreaFooter: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginTop: 8,
  },
  charCount: {
    fontSize: 12,
    color: COLORS.gray,
  },
  validIndicator: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 4,
  },
  validText: {
    fontSize: 12,
    color: COLORS.success,
    fontWeight: '600',
  },
  inputError: {
    borderWidth: 1,
    borderColor: COLORS.error,
  },
  errorText: {
    fontSize: 12,
    color: COLORS.error,
    marginTop: 5,
    marginLeft: 5,
  },
  submitButton: {
    marginTop: 10,
    marginBottom: 20,
    borderRadius: 15,
    overflow: 'hidden',
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
  },
  submitButtonDisabled: {
    opacity: 0.7,
  },
  submitGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 16,
    gap: 10,
  },
  submitButtonText: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  notesCard: {
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 20,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
    marginBottom: 20,
  },
  notesHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 15,
    gap: 10,
  },
  notesTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  noteItem: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 10,
    gap: 12,
    paddingLeft: 5,
  },
  noteText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.gray,
    lineHeight: 18,
  },
});