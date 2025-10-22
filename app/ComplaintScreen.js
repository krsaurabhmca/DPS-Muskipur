import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
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

const COMPLAINT_TO_OPTIONS = [
  { label: 'ADMIN', value: 'ADMIN' },
  { label: 'ACCOUNT', value: 'ACCOUNT' },
];

export default function ComplaintScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    complaint_date: new Date().toISOString().split('T')[0], // YYYY-MM-DD
    complaint_to: 'ADMIN',
    complaint: '',
  });
  const [showDropdown, setShowDropdown] = useState(false);
  const [errors, setErrors] = useState({});

  const validateForm = () => {
    const newErrors = {};

    if (!formData.complaint_date) {
      newErrors.complaint_date = 'Date is required';
    }

    if (!formData.complaint_to) {
      newErrors.complaint_to = 'Please select recipient';
    }

    if (!formData.complaint.trim()) {
      newErrors.complaint = 'Complaint message is required';
    } else if (formData.complaint.trim().length < 10) {
      newErrors.complaint = 'Complaint must be at least 10 characters';
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
        complaint_date: formData.complaint_date,
        complaint_to: formData.complaint_to,
        complaint: formData.complaint.trim(),
        status: 'ACTIVE',
      };

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=send_complaint',
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
          'Success', 'Your complaint has been submitted successfully!',
          [
            {
              text: 'OK',
              onPress: () => {
                // Reset form
                setFormData({
                  complaint_date: new Date().toISOString().split('T')[0],
                  complaint_to: 'ADMIN',
                  complaint: '',
                });
                setErrors({});
                // Optionally go back
                // router.back();
              },
            },
          ]
        );
      } else {
        Alert.alert('Error', data.msg || 'Failed to submit complaint');
      }
    } catch (err) {
      console.error('Error submitting complaint:', err);
      Alert.alert('Error', 'Network error. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const formatDateForDisplay = (dateString) => {
    const date = new Date(dateString);
    const options = { day: '2-digit', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('en-GB', options);
  };

  const handleDateChange = (days) => {
    const currentDate = new Date(formData.complaint_date);
    currentDate.setDate(currentDate.getDate() + days);
    setFormData({
      ...formData,
      complaint_date: currentDate.toISOString().split('T')[0],
    });
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
          <Text style={styles.headerTitle}>Submit Complaint</Text>
          <View style={{ width: 40 }} />
        </View>

        {/* Info Card */}
        <View style={styles.infoCard}>
          <View style={styles.infoIconContainer}>
            <Ionicons name="chatbox-ellipses" size={32} color={COLORS.secondary} />
          </View>
          <Text style={styles.infoText}>
            Share your concerns and we'll address them promptly
          </Text>
        </View>
      </LinearGradient>

      <ScrollView
        style={styles.scrollView}
        showsVerticalScrollIndicator={false}
        keyboardShouldPersistTaps="handled"
      >
        <View style={styles.formContainer}>
          {/* Date Selector */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>
              <Ionicons name="calendar" size={16} color={COLORS.primary} /> Date
            </Text>
            <View style={styles.dateSelector}>
              <TouchableOpacity
                style={styles.dateButton}
                onPress={() => handleDateChange(-1)}
              >
                <Ionicons name="chevron-back" size={20} color={COLORS.secondary} />
              </TouchableOpacity>
              
              <View style={styles.dateDisplay}>
                <Text style={styles.dateText}>
                  {formatDateForDisplay(formData.complaint_date)}
                </Text>
              </View>
              
              <TouchableOpacity
                style={styles.dateButton}
                onPress={() => handleDateChange(1)}
              >
                <Ionicons name="chevron-forward" size={20} color={COLORS.secondary} />
              </TouchableOpacity>
            </View>
            {errors.complaint_date && (
              <Text style={styles.errorText}>{errors.complaint_date}</Text>
            )}
          </View>

          {/* Complaint To Dropdown */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>
              <Ionicons name="person" size={16} color={COLORS.primary} /> Send To
            </Text>
            <TouchableOpacity
              style={[styles.dropdown, errors.complaint_to && styles.inputError]}
              onPress={() => setShowDropdown(!showDropdown)}
            >
              <Text style={styles.dropdownText}>{formData.complaint_to}</Text>
              <Ionicons
                name={showDropdown ? 'chevron-up' : 'chevron-down'}
                size={20}
                color={COLORS.gray}
              />
            </TouchableOpacity>
            
            {showDropdown && (
              <View style={styles.dropdownMenu}>
                {COMPLAINT_TO_OPTIONS.map((option) => (
                  <TouchableOpacity
                    key={option.value}
                    style={[
                      styles.dropdownItem,
                      formData.complaint_to === option.value && styles.dropdownItemActive,
                    ]}
                    onPress={() => {
                      setFormData({ ...formData, complaint_to: option.value });
                      setShowDropdown(false);
                      setErrors({ ...errors, complaint_to: null });
                    }}
                  >
                    <Text
                      style={[
                        styles.dropdownItemText,
                        formData.complaint_to === option.value && styles.dropdownItemTextActive,
                      ]}
                    >
                      {option.label}
                    </Text>
                    {formData.complaint_to === option.value && (
                      <Ionicons name="checkmark" size={20} color={COLORS.white} />
                    )}
                  </TouchableOpacity>
                ))}
              </View>
            )}
            {errors.complaint_to && (
              <Text style={styles.errorText}>{errors.complaint_to}</Text>
            )}
          </View>

          {/* Complaint Message */}
          <View style={styles.inputGroup}>
            <Text style={styles.label}>
              <Ionicons name="chatbubble-ellipses" size={16} color={COLORS.primary} /> Your Complaint
            </Text>
            <View style={[styles.textAreaContainer, errors.complaint && styles.inputError]}>
              <TextInput
                style={styles.textArea}
                placeholder="Describe your complaint in detail..."
                placeholderTextColor={COLORS.gray}
                multiline
                numberOfLines={8}
                textAlignVertical="top"
                value={formData.complaint}
                onChangeText={(text) => {
                  setFormData({ ...formData, complaint: text });
                  setErrors({ ...errors, complaint: null });
                }}
              />
              <Text style={styles.charCount}>
                {formData.complaint.length} characters
              </Text>
            </View>
            {errors.complaint && (
              <Text style={styles.errorText}>{errors.complaint}</Text>
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
                <ActivityIndicator size="small" color={COLORS.white} />
              ) : (
                <>
                  <Ionicons name="send" size={20} color={COLORS.white} />
                  <Text style={styles.submitButtonText}>Submit Complaint</Text>
                </>
              )}
            </LinearGradient>
          </TouchableOpacity>

          {/* Tips Card */}
          <View style={styles.tipsCard}>
            <View style={styles.tipsHeader}>
              <Ionicons name="bulb" size={20} color={COLORS.accent} />
              <Text style={styles.tipsTitle}>Tips for Better Response</Text>
            </View>
            <View style={styles.tipItem}>
              <Ionicons name="checkmark-circle" size={16} color={COLORS.secondary} />
              <Text style={styles.tipText}>Be specific and clear in your complaint</Text>
            </View>
            <View style={styles.tipItem}>
              <Ionicons name="checkmark-circle" size={16} color={COLORS.secondary} />
              <Text style={styles.tipText}>Provide relevant details and context</Text>
            </View>
            <View style={styles.tipItem}>
              <Ionicons name="checkmark-circle" size={16} color={COLORS.secondary} />
              <Text style={styles.tipText}>Use respectful and professional language</Text>
            </View>
          </View>
        </View>
      </ScrollView>
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
    alignItems: 'center',
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
  infoText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.gray,
    lineHeight: 18,
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
  dateSelector: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    borderRadius: 15,
    overflow: 'hidden',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  dateButton: {
    padding: 15,
    backgroundColor: COLORS.white,
  },
  dateDisplay: {
    flex: 1,
    alignItems: 'center',
    paddingVertical: 15,
    backgroundColor: COLORS.secondary + '10',
  },
  dateText: {
    fontSize: 15,
    fontWeight: '600',
    color: COLORS.primary,
  },
  dropdown: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 15,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  dropdownText: {
    fontSize: 15,
    color: COLORS.primary,
    fontWeight: '500',
  },
  dropdownMenu: {
    marginTop: 10,
    backgroundColor: COLORS.white,
    borderRadius: 15,
    overflow: 'hidden',
    elevation: 3,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.15,
    shadowRadius: 3,
  },
  dropdownItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: 15,
    borderBottomWidth: 1,
    borderBottomColor: COLORS.lightGray,
  },
  dropdownItemActive: {
    backgroundColor: COLORS.secondary,
  },
  dropdownItemText: {
    fontSize: 15,
    color: COLORS.primary,
  },
  dropdownItemTextActive: {
    color: COLORS.white,
    fontWeight: '600',
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
    minHeight: 150,
    textAlignVertical: 'top',
  },
  charCount: {
    fontSize: 12,
    color: COLORS.gray,
    textAlign: 'right',
    marginTop: 8,
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
  tipsCard: {
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 20,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  tipsHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 15,
    gap: 10,
  },
  tipsTitle: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  tipItem: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 10,
    gap: 10,
  },
  tipText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.gray,
    lineHeight: 18,
  },
});