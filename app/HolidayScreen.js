import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useState } from 'react';
import {
    Dimensions,
    RefreshControl,
    ScrollView,
    StyleSheet,
    Text,
    TouchableOpacity,
    View
} from 'react-native';

const { width } = Dimensions.get('window');

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
  holiday: '#FF6B6B',
};

const HOLIDAY_ICONS = [
  'balloon',
  'gift',
  'star',
  'heart',
  'trophy',
  'flower',
  'sparkles',
  'sunny',
];

const MONTHS = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

export default function HolidayListScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [holidays, setHolidays] = useState([]);
  const [groupedHolidays, setGroupedHolidays] = useState({});
  const [error, setError] = useState('');
  const [selectedYear, setSelectedYear] = useState(new Date().getFullYear());

  useEffect(() => {
    fetchHolidays();
  }, []);

  useEffect(() => {
    groupHolidaysByMonth();
  }, [holidays]);

  const fetchHolidays = async () => {
    try {
      setLoading(true);
      setError('');

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=holiday_list',
        {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
          },
        }
      );

      const result = await response.json();
      
      if (result.status === 'success' && result.data && Array.isArray(result.data)) {
        // Sort by date
        const sortedHolidays = result.data.sort((a, b) => 
          new Date(a.holiday_date) - new Date(b.holiday_date)
        );
        setHolidays(sortedHolidays);
        
        // Cache holidays
        await AsyncStorage.setItem('cached_holidays', JSON.stringify(sortedHolidays));
      } else {
        setError('No holidays found');
      }
    } catch (err) {
      console.error('Error fetching holidays:', err);
      setError('Failed to load holidays');
      
      // Try to load cached holidays
      try {
        const cachedHolidays = await AsyncStorage.getItem('cached_holidays');
        if (cachedHolidays) {
          setHolidays(JSON.parse(cachedHolidays));
          setError('Showing cached holidays. Network error occurred.');
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
    fetchHolidays();
  };

  const groupHolidaysByMonth = () => {
    const grouped = {};
    
    holidays.forEach(holiday => {
      const date = new Date(holiday.holiday_date);
      const monthYear = `${MONTHS[date.getMonth()]} ${date.getFullYear()}`;
      
      if (!grouped[monthYear]) {
        grouped[monthYear] = [];
      }
      grouped[monthYear].push(holiday);
    });
    
    setGroupedHolidays(grouped);
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const options = { day: '2-digit', month: 'short', year: 'numeric' };
    return date.toLocaleDateString('en-GB', options);
  };

  const getDayOfWeek = (dateString) => {
    const date = new Date(dateString);
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return days[date.getDay()];
  };

  const isUpcoming = (dateString) => {
    const holidayDate = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return holidayDate >= today;
  };

  const isPast = (dateString) => {
    const holidayDate = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    return holidayDate < today;
  };

  const getDaysUntil = (dateString) => {
    const holidayDate = new Date(dateString);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const diffTime = holidayDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
  };

  const getUpcomingHolidays = () => {
    return holidays.filter(h => isUpcoming(h.holiday_date));
  };

  const getRandomIcon = (index) => {
    return HOLIDAY_ICONS[index % HOLIDAY_ICONS.length];
  };

  const getRandomColor = (index) => {
    const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'];
    return colors[index % colors.length];
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <View style={styles.logoCircle}>
          <Ionicons name="calendar" size={40} color={COLORS.primary} />
        </View>
        <Text style={styles.loadingText}>Loading holidays...</Text>
      </View>
    );
  }

  const upcomingCount = getUpcomingHolidays().length;

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
          <Text style={styles.headerTitle}>Holiday List</Text>
          <View style={{ width: 40 }} />
        </View>

        {/* Stats */}
        <View style={styles.statsContainer}>
          <View style={styles.statItem}>
            <View style={styles.statIcon}>
              <Ionicons name="calendar" size={28} color={COLORS.white} />
            </View>
            <Text style={styles.statValue}>{holidays.length}</Text>
            <Text style={styles.statLabel}>Total Holidays</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statItem}>
            <View style={styles.statIcon}>
              <Ionicons name="time" size={28} color={COLORS.white} />
            </View>
            <Text style={styles.statValue}>{upcomingCount}</Text>
            <Text style={styles.statLabel}>Upcoming</Text>
          </View>
        </View>

        {/* Year Badge */}
        <View style={styles.yearBadge}>
          <Ionicons name="calendar-outline" size={18} color={COLORS.primary} />
          <Text style={styles.yearText}>Academic Year {selectedYear}</Text>
        </View>
      </LinearGradient>

      {/* Error Banner */}
      {error && (
        <View style={styles.errorBanner}>
          <Ionicons name="information-circle" size={16} color={COLORS.error} />
          <Text style={styles.errorBannerText}>{error}</Text>
        </View>
      )}

      {/* Holiday List */}
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
        {holidays.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="calendar-outline" size={80} color={COLORS.lightGray} />
            <Text style={styles.emptyText}>No holidays scheduled</Text>
            <Text style={styles.emptySubText}>Check back later for updates</Text>
          </View>
        ) : (
          <>
            {/* Next Holiday Card */}
            {upcomingCount > 0 && (
              <View style={styles.nextHolidaySection}>
                <Text style={styles.sectionTitle}>
                  <Ionicons name="star" size={18} color={COLORS.accent} /> Next Holiday
                </Text>
                <NextHolidayCard
                  holiday={getUpcomingHolidays()[0]}
                  formatDate={formatDate}
                  getDayOfWeek={getDayOfWeek}
                  getDaysUntil={getDaysUntil}
                  getRandomColor={getRandomColor}
                />
              </View>
            )}

            {/* Grouped Holidays */}
            {Object.keys(groupedHolidays).map((monthYear, groupIndex) => (
              <View key={monthYear} style={styles.monthSection}>
                <View style={styles.monthHeader}>
                  <Ionicons name="calendar" size={20} color={COLORS.secondary} />
                  <Text style={styles.monthTitle}>{monthYear}</Text>
                  <View style={styles.monthBadge}>
                    <Text style={styles.monthBadgeText}>
                      {groupedHolidays[monthYear].length}
                    </Text>
                  </View>
                </View>

                {groupedHolidays[monthYear].map((holiday, index) => (
                  <HolidayCard
                    key={holiday.id}
                    holiday={holiday}
                    index={groupIndex * 10 + index}
                    formatDate={formatDate}
                    getDayOfWeek={getDayOfWeek}
                    isUpcoming={isUpcoming(holiday.holiday_date)}
                    isPast={isPast(holiday.holiday_date)}
                    getDaysUntil={getDaysUntil}
                    getRandomIcon={getRandomIcon}
                    getRandomColor={getRandomColor}
                  />
                ))}
              </View>
            ))}
          </>
        )}
      </ScrollView>
    </View>
  );
}

function NextHolidayCard({ holiday, formatDate, getDayOfWeek, getDaysUntil, getRandomColor }) {
  const daysUntil = getDaysUntil(holiday.holiday_date);
  
  return (
    <View style={styles.nextHolidayCard}>
      <LinearGradient
        colors={['#FF6B6B', '#FF8E53']}
        style={styles.nextHolidayGradient}
        start={{ x: 0, y: 0 }}
        end={{ x: 1, y: 1 }}
      >
        <View style={styles.nextHolidayIcon}>
          <Ionicons name="star" size={32} color={COLORS.white} />
        </View>
        <View style={styles.nextHolidayContent}>
          <Text style={styles.nextHolidayName}>{holiday.holiday_name}</Text>
          <Text style={styles.nextHolidayDate}>
            {getDayOfWeek(holiday.holiday_date)}, {formatDate(holiday.holiday_date)}
          </Text>
          <View style={styles.nextHolidayCountdown}>
            <Ionicons name="time-outline" size={16} color={COLORS.white} />
            <Text style={styles.nextHolidayCountdownText}>
              {daysUntil === 0 ? 'Today!' : daysUntil === 1 ? 'Tomorrow!' : `in ${daysUntil} days`}
            </Text>
          </View>
        </View>
      </LinearGradient>
    </View>
  );
}

function HolidayCard({ 
  holiday, 
  index, 
  formatDate, 
  getDayOfWeek, 
  isUpcoming, 
  isPast, 
  getDaysUntil,
  getRandomIcon, 
  getRandomColor 
}) {
  const color = getRandomColor(index);
  const icon = getRandomIcon(index);
  const daysUntil = getDaysUntil(holiday.holiday_date);

  return (
    <View style={[styles.holidayCard, isPast && styles.holidayCardPast]}>
      <View style={[styles.holidayIconContainer, { backgroundColor: color + '20' }]}>
        <Ionicons name={icon} size={28} color={color} />
      </View>

      <View style={styles.holidayContent}>
        <Text style={[styles.holidayName, isPast && styles.holidayNamePast]}>
          {holiday.holiday_name}
        </Text>
        <View style={styles.holidayDateRow}>
          <Ionicons 
            name="calendar-outline" 
            size={14} 
            color={isPast ? COLORS.gray : COLORS.secondary} 
          />
          <Text style={[styles.holidayDate, isPast && styles.holidayDatePast]}>
            {getDayOfWeek(holiday.holiday_date)}
          </Text>
        </View>
        <Text style={[styles.holidayDateFull, isPast && styles.holidayDatePast]}>
          {formatDate(holiday.holiday_date)}
        </Text>
      </View>

      {isUpcoming && (
        <View style={styles.holidayBadge}>
          {daysUntil === 0 ? (
            <View style={[styles.todayBadge, { backgroundColor: color }]}>
              <Text style={styles.todayBadgeText}>Today</Text>
            </View>
          ) : daysUntil === 1 ? (
            <View style={[styles.tomorrowBadge, { backgroundColor: color }]}>
              <Text style={styles.tomorrowBadgeText}>Tomorrow</Text>
            </View>
          ) : daysUntil <= 7 ? (
            <View style={[styles.soonBadge, { borderColor: color }]}>
              <Text style={[styles.soonBadgeText, { color: color }]}>
                {daysUntil}d
              </Text>
            </View>
          ) : null}
        </View>
      )}

      {isPast && (
        <View style={styles.pastBadge}>
          <Ionicons name="checkmark-circle" size={20} color={COLORS.gray} />
        </View>
      )}
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
    paddingBottom: 25,
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
    marginBottom: 15,
    borderRadius: 15,
    padding: 15,
  },
  statItem: {
    flex: 1,
    alignItems: 'center',
  },
  statIcon: {
    marginBottom: 8,
  },
  statDivider: {
    width: 1,
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
  },
  statValue: {
    fontSize: 24,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  statLabel: {
    fontSize: 12,
    color: COLORS.white,
    opacity: 0.9,
    marginTop: 4,
  },
  yearBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'center',
    backgroundColor: COLORS.white,
    paddingHorizontal: 20,
    paddingVertical: 8,
    borderRadius: 20,
    gap: 8,
  },
  yearText: {
    fontSize: 14,
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
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 80,
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
  nextHolidaySection: {
    paddingHorizontal: 20,
    paddingTop: 20,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 12,
  },
  nextHolidayCard: {
    borderRadius: 20,
    overflow: 'hidden',
    elevation: 5,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.2,
    shadowRadius: 5,
    marginBottom: 10,
  },
  nextHolidayGradient: {
    flexDirection: 'row',
    padding: 20,
    gap: 15,
  },
  nextHolidayIcon: {
    width: 60,
    height: 60,
    borderRadius: 30,
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  nextHolidayContent: {
    flex: 1,
    justifyContent: 'center',
  },
  nextHolidayName: {
    fontSize: 20,
    fontWeight: 'bold',
    color: COLORS.white,
    marginBottom: 4,
  },
  nextHolidayDate: {
    fontSize: 14,
    color: COLORS.white,
    opacity: 0.95,
    marginBottom: 8,
  },
  nextHolidayCountdown: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: 'rgba(255, 255, 255, 0.25)',
    alignSelf: 'flex-start',
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 15,
    gap: 6,
  },
  nextHolidayCountdownText: {
    fontSize: 13,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  monthSection: {
    paddingHorizontal: 20,
    paddingTop: 20,
  },
  monthHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 15,
    gap: 10,
  },
  monthTitle: {
    flex: 1,
    fontSize: 18,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  monthBadge: {
    backgroundColor: COLORS.secondary,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 12,
  },
  monthBadgeText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  holidayCard: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    borderRadius: 15,
    padding: 15,
    marginBottom: 12,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 2,
  },
  holidayCardPast: {
    opacity: 0.6,
  },
  holidayIconContainer: {
    width: 56,
    height: 56,
    borderRadius: 28,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 15,
  },
  holidayContent: {
    flex: 1,
  },
  holidayName: {
    fontSize: 16,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 4,
  },
  holidayNamePast: {
    color: COLORS.gray,
  },
  holidayDateRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    marginBottom: 2,
  },
  holidayDate: {
    fontSize: 13,
    color: COLORS.secondary,
    fontWeight: '600',
  },
  holidayDatePast: {
    color: COLORS.gray,
  },
  holidayDateFull: {
    fontSize: 12,
    color: COLORS.gray,
  },
  holidayBadge: {
    marginLeft: 10,
  },
  todayBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  todayBadgeText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  tomorrowBadge: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 12,
  },
  tomorrowBadgeText: {
    fontSize: 11,
    fontWeight: 'bold',
    color: COLORS.white,
  },
  soonBadge: {
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 12,
    borderWidth: 2,
  },
  soonBadgeText: {
    fontSize: 12,
    fontWeight: 'bold',
  },
  pastBadge: {
    marginLeft: 10,
  },
});