import { FontAwesome5 } from "@expo/vector-icons";
import axios from "axios";
import { BlurView } from "expo-blur";
import * as Haptics from "expo-haptics";
import { LinearGradient } from "expo-linear-gradient";
import { router } from "expo-router";
import { useState } from "react";
import {
  ActivityIndicator,
  Animated,
  FlatList,
  Image,
  Keyboard,
  StatusBar,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
} from "react-native";
import { SafeAreaProvider, SafeAreaView } from "react-native-safe-area-context";

export default function SearchStudentScreen({ navigation }) {
  const [searchText, setSearchText] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [students, setStudents] = useState([]);
  const [error, setError] = useState(null);
  const [recentSearches, setRecentSearches] = useState([
    "RAHUL",
    "PRIYA",
    "ANKIT",
  ]);
  const [hasSearched, setHasSearched] = useState(false);

  // Animation values
  const fadeAnim = useState(new Animated.Value(0))[0];
  const slideAnim = useState(new Animated.Value(50))[0];

  // Function to handle search
  const handleSearch = async (text = searchText) => {
    if (!text.trim()) return;

    Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Light);
    setIsLoading(true);
    setError(null);
    setHasSearched(true);
    Keyboard.dismiss();

    try {
      const response = await axios.post(
        "https://dpsmushkipur.com/bine/api.php?task=search_student",
        { search_text: text }
      );

      if (response.data.status === "success") {
        // Animate results
        Animated.parallel([
          Animated.timing(fadeAnim, {
            toValue: 1,
            duration: 500,
            useNativeDriver: true,
          }),
          Animated.timing(slideAnim, {
            toValue: 0,
            duration: 500,
            useNativeDriver: true,
          }),
        ]).start();

        setStudents(response.data.data);

        // Add to recent searches if not already there
        if (!recentSearches.includes(text.toUpperCase()) && text.trim()) {
          setRecentSearches((prev) => [
            text.toUpperCase(),
            ...prev.slice(0, 4),
          ]);
        }
      } else {
        setError("No students found");
        setStudents([]);
      }
    } catch (err) {
      console.error("Search error:", err);
      setError("Failed to search. Please check your connection.");
      setStudents([]);
    } finally {
      setIsLoading(false);
    }
  };

  // Function to clear search
  const clearSearch = () => {
    setSearchText("");
    setStudents([]);
    setError(null);
    setHasSearched(false);
  };

  // Function to handle recent search click
  const handleRecentSearch = (text) => {
    setSearchText(text);
    handleSearch(text);
  };

  // Student item component
  const renderStudentCard = ({ item, index }) => {
    // Generate a consistent avatar letter from the student name
    const avatarLetter = item.student_name.charAt(0);
    // Generate a consistent color based on the student ID
    const avatarColor = `hsl(${(parseInt(item.id) * 55) % 360}, 70%, 50%)`;

    return (
      <Animated.View
        style={[
          styles.studentCard,
          {
            opacity: fadeAnim,
            transform: [
              {
                translateY: Animated.multiply(
                  slideAnim,
                  new Animated.Value(index + 1)
                ),
              },
            ],
          },
        ]}
      >
        <View
          style={[styles.avatarContainer, { backgroundColor: avatarColor }]}
        >
          <Text style={styles.avatarText}>{avatarLetter}</Text>
        </View>
        <View style={styles.studentInfo}>
          <View style={styles.nameRow}>
            <Text style={styles.studentName}>{item.student_name}</Text>
            <View style={styles.statusPill}>
              <Text style={styles.statusText}>{item.status}</Text>
            </View>
          </View>

          <View style={styles.detailsRow}>
            <View style={styles.detailItem}>
              <FontAwesome5 name="graduation-cap" size={12} color="#7f8c8d" />
              <Text style={styles.detailText}>
                Class {item.student_class}-{item.student_section}
              </Text>
            </View>
            <View style={styles.detailItem}>
              <FontAwesome5 name="user" size={12} color="#7f8c8d" />
              <Text style={styles.detailText}>{item.student_father}</Text>
            </View>
            <View style={styles.detailItem}>
              <FontAwesome5 name="phone-alt" size={12} color="#7f8c8d" />
              <Text style={styles.detailText}>{item.student_mobile}</Text>
            </View>
          </View>

          <View style={styles.actionRow}>
            <TouchableOpacity
              style={[styles.actionButton, styles.attendanceButton]}
              onPress={() => {
                Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);
                // Navigate to attendance screen with student data
              }}
            >
              <FontAwesome5 name="clipboard-check" size={14} color="#ffffff" />
              <Text style={styles.actionButtonText}>Attendance</Text>
            </TouchableOpacity>

            <TouchableOpacity
              style={[styles.actionButton, styles.feesButton]}
              onPress={() => {
                Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);
                // Navigate to fee payment screen with student data
                router.push({
                  pathname: "/PayFee", // Update this to your actual fee screen path
                  params: {
                    student_id: item.id,
                    student_name: item.student_name, 
                    student_class: item.student_class,
                    student_section: item.student_section,
                    student_father: item.student_father,
                    student_mobile: item.student_mobile,
                    admission_no:
                      item.admission_no ||
                      `DPS/${new Date().getFullYear()}/${item.id}`,
                  },
                });
              }}
            >
              <FontAwesome5 name="money-bill-wave" size={14} color="#ffffff" />
              <Text style={styles.actionButtonText}>Pay Fees</Text>
            </TouchableOpacity>

            {/* <TouchableOpacity 
              style={[styles.actionButton, styles.profileButton]}
              onPress={() => {
                Haptics.impactAsync(Haptics.ImpactFeedbackStyle.Medium);
                // Navigate to student profile screen with student data
              }}
            >
              <FontAwesome5 name="id-card" size={14} color="#ffffff" />
              <Text style={styles.actionButtonText}>Profile</Text>
            </TouchableOpacity> */}
          </View>
        </View>
      </Animated.View>
    );
  };

  // Recent search pill component
  const renderRecentSearch = ({ item }) => (
    <TouchableOpacity
      style={styles.recentPill}
      onPress={() => handleRecentSearch(item)}
    >
      <FontAwesome5
        name="history"
        size={12}
        color="#7f8c8d"
        style={styles.recentIcon}
      />
      <Text style={styles.recentText}>{item}</Text>
    </TouchableOpacity>
  );

  return (
    <SafeAreaProvider>
      <SafeAreaView style={styles.container} edges={["bottom"]}>
        <StatusBar barStyle="light-content" backgroundColor="#1e3c72" />

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
            <Text style={styles.headerTitle}>Search Students</Text>
            <View style={styles.placeholder} />
          </View>
        </View>

        <View style={styles.searchContainer}>
          <View style={styles.searchInputContainer}>
            <FontAwesome5
              name="search"
              size={18}
              color="#7f8c8d"
              style={styles.searchIcon}
            />
            <TextInput
              style={styles.searchInput}
              placeholder="Search by name..."
              placeholderTextColor="#95a5a6"
              value={searchText}
              onChangeText={setSearchText}
              onSubmitEditing={() => handleSearch()}
              returnKeyType="search"
              autoCapitalize="words"
            />
            {searchText.length > 0 && (
              <TouchableOpacity onPress={clearSearch}>
                <FontAwesome5 name="times-circle" size={18} color="#7f8c8d" />
              </TouchableOpacity>
            )}
          </View>

          <TouchableOpacity
            style={styles.searchButton}
            onPress={() => handleSearch()}
          >
            <LinearGradient
              colors={["#1e3c72", "#2a5298"]}
              style={styles.searchBtnGradient}
            >
              <FontAwesome5 name="search" size={20} color="#ffffff" />
            </LinearGradient>
          </TouchableOpacity>
        </View>

        {!hasSearched && recentSearches.length > 0 && (
          <View style={styles.recentContainer}>
            <Text style={styles.recentTitle}>Recent Searches</Text>
            <FlatList
              data={recentSearches}
              renderItem={renderRecentSearch}
              keyExtractor={(item, index) => "recent-" + index}
              horizontal
              showsHorizontalScrollIndicator={false}
              contentContainerStyle={styles.recentList}
            />
          </View>
        )}

        {isLoading ? (
          <View style={styles.centerContainer}>
            <ActivityIndicator size="large" color="#1e3c72" />
            <Text style={styles.loadingText}>Searching for students...</Text>
          </View>
        ) : error ? (
          <View style={styles.centerContainer}>
            <FontAwesome5 name="exclamation-circle" size={50} color="#95a5a6" />
            <Text style={styles.errorText}>{error}</Text>
            <TouchableOpacity
              style={styles.retryButton}
              onPress={() => handleSearch()}
            >
              <Text style={styles.retryText}>Retry Search</Text>
            </TouchableOpacity>
          </View>
        ) : hasSearched && students.length === 0 ? (
          <View style={styles.centerContainer}>
            <Image
              source={require("./assets/no-results.png")} // Replace with appropriate path
              style={styles.noResultsImage}
            />
            <Text style={styles.noResultsTitle}>No Students Found</Text>
            <Text style={styles.noResultsText}>
              Try searching with a different name
            </Text>
          </View>
        ) : (
          <FlatList
            data={students}
            renderItem={renderStudentCard}
            keyExtractor={(item) => item.id}
            contentContainerStyle={styles.listContainer}
            showsVerticalScrollIndicator={false}
            ListEmptyComponent={
              !hasSearched ? (
                <View style={styles.centerContainer}>
                  <Image
                    source={require("./assets/search-illustration.png")} // Replace with appropriate path
                    style={styles.emptyImage}
                  />
                  <Text style={styles.emptyTitle}>Find Students</Text>
                  <Text style={styles.emptyText}>
                    Search by student name to view details
                  </Text>
                </View>
              ) : null
            }
          />
        )}

        {students.length > 0 && (
          <BlurView intensity={80} tint="light" style={styles.fabContainer}>
            <Text style={styles.resultCount}>
              Found {students.length} student{students.length !== 1 ? "s" : ""}
            </Text>
          </BlurView>
        )}
      </SafeAreaView>
    </SafeAreaProvider>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: "#f8f9fa",
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
    height: 100,
  },
  headerContent: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    padding: 20,
    paddingTop: 20,
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
  placeholder: {
    width: 40,
  },
  searchContainer: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 20,
    marginTop: -20,
    marginBottom: 20,
  },
  searchInputContainer: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderRadius: 12,
    paddingHorizontal: 16,
    height: 56,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  searchIcon: {
    marginRight: 10,
  },
  searchInput: {
    flex: 1,
    fontSize: 16,
    color: "#2c3e50",
  },
  searchButton: {
    marginLeft: 12,
    width: 56,
    height: 56,
    borderRadius: 12,
    overflow: "hidden",
    shadowColor: "#1e3c72",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 4,
    elevation: 3,
  },
  searchBtnGradient: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  recentContainer: {
    paddingHorizontal: 20,
    marginBottom: 16,
  },
  recentTitle: {
    fontSize: 16,
    fontWeight: "bold",
    color: "#2c3e50",
    marginBottom: 10,
  },
  recentList: {
    paddingRight: 20,
  },
  recentPill: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#ffffff",
    borderRadius: 20,
    paddingHorizontal: 16,
    paddingVertical: 8,
    marginRight: 10,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 2,
    elevation: 1,
  },
  recentIcon: {
    marginRight: 6,
  },
  recentText: {
    color: "#2c3e50",
    fontSize: 14,
  },
  centerContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    padding: 20,
  },
  loadingText: {
    marginTop: 12,
    fontSize: 16,
    color: "#7f8c8d",
  },
  errorText: {
    marginTop: 12,
    fontSize: 16,
    color: "#7f8c8d",
    textAlign: "center",
  },
  retryButton: {
    marginTop: 16,
    backgroundColor: "#1e3c72",
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 8,
  },
  retryText: {
    color: "#ffffff",
    fontSize: 14,
    fontWeight: "bold",
  },
  noResultsImage: {
    width: 150,
    height: 150,
    marginBottom: 16,
    opacity: 0.8,
  },
  noResultsTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#2c3e50",
    marginBottom: 8,
  },
  noResultsText: {
    fontSize: 16,
    color: "#7f8c8d",
    textAlign: "center",
  },
  emptyImage: {
    width: 180,
    height: 180,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: "bold",
    color: "#2c3e50",
    marginBottom: 8,
  },
  emptyText: {
    fontSize: 16,
    color: "#7f8c8d",
    textAlign: "center",
  },
  listContainer: {
    padding: 20,
    paddingBottom: 100,
  },
  studentCard: {
    backgroundColor: "#ffffff",
    borderRadius: 16,
    marginBottom: 16,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 2,
    flexDirection: "row",
    padding: 16,
  },
  avatarContainer: {
    width: 60,
    height: 60,
    borderRadius: 30,
    justifyContent: "center",
    alignItems: "center",
    marginRight: 16,
  },
  avatarText: {
    color: "#ffffff",
    fontSize: 24,
    fontWeight: "bold",
  },
  studentInfo: {
    flex: 1,
  },
  nameRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 8,
  },
  studentName: {
    fontSize: 18,
    fontWeight: "bold",
    color: "#2c3e50",
  },
  statusPill: {
    backgroundColor: "#e8f5e9",
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  statusText: {
    color: "#388e3c",
    fontSize: 12,
    fontWeight: "600",
  },
  detailsRow: {
    marginBottom: 12,
  },
  detailItem: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 4,
  },
  detailText: {
    fontSize: 14,
    color: "#7f8c8d",
    marginLeft: 8,
  },
  actionRow: {
    flexDirection: "row",
    justifyContent: "space-between",
  },
  actionButton: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 8,
    flex: 1,
    marginHorizontal: 4,
  },
  attendanceButton: {
    backgroundColor: "#3a86ff",
  },
  feesButton: {
    backgroundColor: "#38b000",
  },
  profileButton: {
    backgroundColor: "#6c757d",
  },
  actionButtonText: {
    color: "#ffffff",
    fontSize: 12,
    fontWeight: "bold",
    marginLeft: 6,
  },
  fabContainer: {
    position: "absolute",
    bottom: 20,
    alignSelf: "center",
    paddingHorizontal: 20,
    paddingVertical: 8,
    borderRadius: 20,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    overflow: "hidden",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 4,
    elevation: 4,
  },
  resultCount: {
    fontSize: 14,
    fontWeight: "bold",
    color: "#2c3e50",
  },
});
