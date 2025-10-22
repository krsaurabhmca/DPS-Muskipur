import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { useNavigation } from '@react-navigation/native'; // Add this import
import { router } from 'expo-router';
import { useState } from 'react';
import {
  ActivityIndicator,
  Alert,
  ScrollView,
  StatusBar,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from 'react-native';

const RatingScreen = () => {
  const [rating, setRating] = useState(0);
  const [review, setReview] = useState('');
  const [loading, setLoading] = useState(false);
  const navigation = useNavigation(); // Add this hook
 
  const handleSubmit = async () => {
    const studentId = await AsyncStorage.getItem('student_id');
    // Validation
    if (rating === 0) {
      Alert.alert('Error', 'Please select a rating');
      return;
    }
    if (review.trim() === '') {
      Alert.alert('Error', 'Please write a review');
      return;
    }

    setLoading(true);

    const payload = {
      student_id: studentId,
      rating: rating.toString(),
      review: review,
      status: 'ACTIVE',
      created_by: studentId,
    };

    try {
      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=send_review',
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
        Alert.alert('Success', 'Review submitted successfully!', [
          {
            text: 'OK',
            onPress: () => {
              // Reset form
              setRating(0);
              setReview('');
              router.back(); // Navigate back to the previous screen
            },
          },
        ]);
      } else {
        Alert.alert('Error', 'Failed to submit review. Please try again.');
      }
    } catch (error) {
      console.error('Error:', error);
      Alert.alert('Error', 'Network error. Please check your connection.');
    } finally {
      setLoading(false);
    }
  };

  const renderStars = () => {
    const stars = [];
    for (let i = 1; i <= 5; i++) {
      stars.push(
        <TouchableOpacity
          key={i}
          onPress={() => setRating(i)}
          style={styles.starButton}
        >
          <Ionicons
            name={i <= rating ? 'star' : 'star-outline'}
            size={40}
            color={i <= rating ? '#FFD700' : '#D3D3D3'}
          />
        </TouchableOpacity>
      );
    }
    return stars;
  };

  return (
    <View style={styles.container}>
      <StatusBar barStyle="light-content" backgroundColor="#0B6623" />
      
      {/* Header */}
      <View style={styles.header}>
        {/* Back Button */}
        <TouchableOpacity 
          style={styles.backButton}
          onPress={() => navigation.goBack()}
        >
          <Ionicons name="arrow-back" size={24} color="#FFD700" />
        </TouchableOpacity>

        <View style={styles.headerContent}>
          <Text style={styles.headerTitle}>DPS Mushkipur</Text>
          <Text style={styles.headerSubtitle}>Rate Your Experience</Text>
        </View>
      </View>

      <ScrollView 
        style={styles.content}
        contentContainerStyle={styles.contentContainer}
      >
        {/* Rating Section */}
        <View style={styles.card}>
          <Text style={styles.label}>How would you rate us?</Text>
          <View style={styles.starsContainer}>
            {renderStars()}
          </View>
          {rating > 0 && (
            <Text style={styles.ratingText}>
              {rating} {rating === 1 ? 'Star' : 'Stars'}
            </Text>
          )}
        </View>

        {/* Review Section */}
        <View style={styles.card}>
          <Text style={styles.label}>Write Your Review</Text>
          <TextInput
            style={styles.textInput}
            placeholder="Share your thoughts about DPS Mushkipur..."
            placeholderTextColor="#999"
            multiline
            numberOfLines={6}
            value={review}
            onChangeText={setReview}
            textAlignVertical="top"
          />
          <Text style={styles.charCount}>{review.length} characters</Text>
        </View>

        {/* Submit Button */}
        <TouchableOpacity
          style={[
            styles.submitButton,
            loading && styles.submitButtonDisabled,
          ]}
          onPress={handleSubmit}
          disabled={loading}
        >
          {loading ? (
            <ActivityIndicator color="#FFF" />
          ) : (
            <Text style={styles.submitButtonText}>Submit Review</Text>
          )}
        </TouchableOpacity>

        {/* Info Footer */}
        <View style={styles.footer}>
          <Ionicons name="information-circle-outline" size={20} color="#666" />
          <Text style={styles.footerText}>
            Your feedback helps us improve our services
          </Text>
        </View>
      </ScrollView>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: '#F5F5F5',
  },
  header: {
    backgroundColor: '#0B6623', // Dark Green (DPS Green)
    paddingTop: 60,
    paddingBottom: 30,
    paddingHorizontal: 20,
    borderBottomLeftRadius: 25,
    borderBottomRightRadius: 25,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 5,
    elevation: 8,
    position: 'relative',
  },
  backButton: {
    position: 'absolute',
    top: 60,
    left: 20,
    zIndex: 10,
    padding: 8,
    borderRadius: 8,
    backgroundColor: 'rgba(255, 215, 0, 0.1)',
  },
  headerContent: {
    alignItems: 'center',
  },
  headerTitle: {
    fontSize: 28,
    fontWeight: 'bold',
    color: '#FFD700', // Gold/Yellow
    textAlign: 'center',
    marginBottom: 5,
  },
  headerSubtitle: {
    fontSize: 16,
    color: '#FFF',
    textAlign: 'center',
  },
  content: {
    flex: 1,
  },
  contentContainer: {
    padding: 20,
  },
  card: {
    backgroundColor: '#FFF',
    borderRadius: 15,
    padding: 20,
    marginBottom: 20,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  label: {
    fontSize: 18,
    fontWeight: '600',
    color: '#0B6623',
    marginBottom: 15,
  },
  starsContainer: {
    flexDirection: 'row',
    justifyContent: 'center',
    marginVertical: 10,
  },
  starButton: {
    marginHorizontal: 5,
  },
  ratingText: {
    textAlign: 'center',
    fontSize: 16,
    color: '#FFD700',
    fontWeight: '600',
    marginTop: 10,
  },
  textInput: {
    backgroundColor: '#F9F9F9',
    borderRadius: 10,
    padding: 15,
    fontSize: 16,
    color: '#333',
    minHeight: 120,
    borderWidth: 1,
    borderColor: '#E0E0E0',
  },
  charCount: {
    textAlign: 'right',
    color: '#999',
    fontSize: 12,
    marginTop: 5,
  },
  submitButton: {
    backgroundColor: '#FFD700', // Yellow/Gold
    borderRadius: 12,
    padding: 18,
    alignItems: 'center',
    marginBottom: 20,
    shadowColor: '#FFD700',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.4,
    shadowRadius: 5,
    elevation: 5,
  },
  submitButtonDisabled: {
    opacity: 0.6,
  },
  submitButtonText: {
    color: '#0B6623',
    fontSize: 18,
    fontWeight: 'bold',
  },
  footer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: 20,
  },
  footerText: {
    color: '#666',
    fontSize: 14,
    marginLeft: 8,
  },
});

export default RatingScreen;