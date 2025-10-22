import { Ionicons } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';
import { useRouter } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import {
    Dimensions,
    Linking,
    RefreshControl,
    ScrollView,
    StyleSheet,
    Text,
    TextInput,
    TouchableOpacity,
    View
} from 'react-native';
import * as Animatable from 'react-native-animatable';
import RenderHtml from 'react-native-render-html';

const { width } = Dimensions.get('window');

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

export default function NoticeBoardScreen() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const [notices, setNotices] = useState([]);
  const [filteredNotices, setFilteredNotices] = useState([]);
  const [error, setError] = useState('');
  const [searchQuery, setSearchQuery] = useState('');
  const [selectedFilter, setSelectedFilter] = useState('all');
  const fadeAnim = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    fetchNotices();
  }, []);

  useEffect(() => {
    filterNotices();
  }, [searchQuery, selectedFilter, notices]);

  const fetchNotices = async () => {
    try {
      setLoading(true);
      setError('');

      const studentId = await AsyncStorage.getItem('student_id');
      
      if (!studentId) {
        router.replace('/index');
        return;
      }

      const response = await fetch(
        'https://dpsmushkipur.com/bine/api.php?task=get_notice',
        {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ student_id: studentId }),
        }
      );

      const data = await response.json();
      
      if (data && Array.isArray(data)) {
        // Sort by date (newest first)
        const sortedNotices = data.sort((a, b) => 
          new Date(b.notice_date) - new Date(a.notice_date)
        );
        setNotices(sortedNotices);
        setFilteredNotices(sortedNotices);
        
        // Cache notices
        await AsyncStorage.setItem('cached_notices', JSON.stringify(sortedNotices));
      } else {
        setError('No notices found');
      }

      // Animate
      Animated.timing(fadeAnim, {
        toValue: 1,
        duration: 600,
        useNativeDriver: true,
      }).start();
    } catch (err) {
      console.error('Error fetching notices:', err);
      setError('Failed to load notices');
      
      // Try to load cached notices
      try {
        const cachedNotices = await AsyncStorage.getItem('cached_notices');
        if (cachedNotices) {
          setNotices(JSON.parse(cachedNotices));
          setFilteredNotices(JSON.parse(cachedNotices));
          setError('Showing cached notices. Network error occurred.');
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
    fetchNotices();
  };

  const filterNotices = () => {
    let filtered = notices;

    // Apply search filter
    if (searchQuery.trim()) {
      filtered = filtered.filter(notice =>
        notice.notice_title.toLowerCase().includes(searchQuery.toLowerCase()) ||
        notice.notice_details.toLowerCase().includes(searchQuery.toLowerCase())
      );
    }

    // Apply time filter
    const today = new Date();
    const oneWeekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    const oneMonthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);

    switch (selectedFilter) {
      case 'week':
        filtered = filtered.filter(notice => 
          new Date(notice.notice_date) >= oneWeekAgo
        );
        break;
      case 'month':
        filtered = filtered.filter(notice => 
          new Date(notice.notice_date) >= oneMonthAgo
        );
        break;
      case 'all':
      default:
        break;
    }

    setFilteredNotices(filtered);
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
      return 'Today';
    } else if (date.toDateString() === yesterday.toDateString()) {
      return 'Yesterday';
    } else {
      const options = { day: '2-digit', month: 'short', year: 'numeric' };
      return date.toLocaleDateString('en-GB', options);
    }
  };

  const getTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return '1 day ago';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
    return `${Math.floor(diffDays / 365)} years ago`;
  };

  const handleAttachment = (url) => {
    if (url) {
      Linking.openURL(`https://dpsmushkipur.com/bine/uploads/${url}`);
    }
  };

  if (loading) {
    return (
      <View style={styles.loadingContainer}>
        <Animatable.View 
          animation="pulse" 
          easing="ease-out" 
          iterationCount="infinite"
        >
          <View style={styles.logoCircle}>
            <Ionicons name="notifications" size={40} color={COLORS.primary} />
          </View>
        </Animatable.View>
        <Text style={styles.loadingText}>Loading notices...</Text>
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
          <Text style={styles.headerTitle}>Notice Board</Text>
          <View style={{ width: 40 }} />
        </View>

        {/* Stats */}
        <View style={styles.statsContainer}>
          <View style={styles.statItem}>
            <Text style={styles.statValue}>{notices.length}</Text>
            <Text style={styles.statLabel}>Total Notices</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statItem}>
            <Text style={styles.statValue}>
              {notices.filter(n => {
                const date = new Date(n.notice_date);
                const weekAgo = new Date();
                weekAgo.setDate(weekAgo.getDate() - 7);
                return date >= weekAgo;
              }).length}
            </Text>
            <Text style={styles.statLabel}>This Week</Text>
          </View>
        </View>

        {/* Search Bar */}
        <View style={styles.searchContainer}>
          <Ionicons name="search" size={20} color={COLORS.gray} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search notices..."
            placeholderTextColor={COLORS.gray}
            value={searchQuery}
            onChangeText={setSearchQuery}
          />
          {searchQuery ? (
            <TouchableOpacity onPress={() => setSearchQuery('')}>
              <Ionicons name="close-circle" size={20} color={COLORS.gray} />
            </TouchableOpacity>
          ) : null}
        </View>
      </LinearGradient>

      {/* Filter Buttons */}
      <View style={styles.filterContainer}>
        <FilterButton
          title="All"
          active={selectedFilter === 'all'}
          onPress={() => setSelectedFilter('all')}
          count={notices.length}
        />
        <FilterButton
          title="This Week"
          active={selectedFilter === 'week'}
          onPress={() => setSelectedFilter('week')}
          count={notices.filter(n => {
            const date = new Date(n.notice_date);
            const weekAgo = new Date();
            weekAgo.setDate(weekAgo.getDate() - 7);
            return date >= weekAgo;
          }).length}
        />
        <FilterButton
          title="This Month"
          active={selectedFilter === 'month'}
          onPress={() => setSelectedFilter('month')}
          count={notices.filter(n => {
            const date = new Date(n.notice_date);
            const monthAgo = new Date();
            monthAgo.setDate(monthAgo.getDate() - 30);
            return date >= monthAgo;
          }).length}
        />
      </View>

      {/* Error Banner */}
      {error && (
        <View style={styles.errorBanner}>
          <Ionicons name="alert-circle" size={16} color={COLORS.error} />
          <Text style={styles.errorBannerText}>{error}</Text>
        </View>
      )}

      {/* Notices List */}
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
        {filteredNotices.length === 0 ? (
          <View style={styles.emptyContainer}>
            <Ionicons name="document-text-outline" size={80} color={COLORS.lightGray} />
            <Text style={styles.emptyText}>
              {searchQuery ? 'No notices found matching your search' : 'No notices available'}
            </Text>
          </View>
        ) : (
          filteredNotices.map((notice, index) => (
            <Animatable.View
              key={notice.id}
              animation="fadeInUp"
              delay={index * 100}
              style={{ opacity: fadeAnim }}
            >
              <NoticeCard
                notice={notice}
                formatDate={formatDate}
                getTimeAgo={getTimeAgo}
                handleAttachment={handleAttachment}
              />
            </Animatable.View>
          ))
        )}
      </ScrollView>
    </View>
  );
}

function FilterButton({ title, active, onPress, count }) {
  return (
    <TouchableOpacity
      style={[styles.filterButton, active && styles.filterButtonActive]}
      onPress={onPress}
    >
      <Text style={[styles.filterButtonText, active && styles.filterButtonTextActive]}>
        {title}
      </Text>
      {count > 0 && (
        <View style={[styles.countBadge, active && styles.countBadgeActive]}>
          <Text style={[styles.countText, active && styles.countTextActive]}>
            {count}
          </Text>
        </View>
      )}
    </TouchableOpacity>
  );
}

function NoticeCard({ notice, formatDate, getTimeAgo, handleAttachment }) {
  const [expanded, setExpanded] = useState(false);
  
  // Remove HTML tags for preview
  const stripHtml = (html) => {
    return html.replace(/<br\s*\/?>/gi, '\n').replace(/<[^>]*>/g, '');
  };

  const previewText = stripHtml(notice.notice_details);
  const shouldShowMore = previewText.length > 150;

  // HTML rendering config
  const htmlConfig = {
    width: width - 80,
  };

  const tagsStyles = {
    body: {
      color: COLORS.gray,
      fontSize: 14,
      lineHeight: 22,
    },
    br: {
      height: 8,
    },
  };

  return (
    <TouchableOpacity
      style={styles.noticeCard}
      activeOpacity={0.7}
      onPress={() => setExpanded(!expanded)}
    >
      {/* Date Badge */}
      <View style={styles.dateBadge}>
        <LinearGradient
          colors={[COLORS.accent, '#FFD54F']}
          style={styles.dateBadgeGradient}
        >
          <Ionicons name="calendar" size={14} color={COLORS.primary} />
          <Text style={styles.dateText}>{formatDate(notice.notice_date)}</Text>
        </LinearGradient>
      </View>

      {/* Notice Content */}
      <View style={styles.noticeContent}>
        <View style={styles.noticeHeader}>
          <View style={styles.iconContainer}>
            <Ionicons name="notifications" size={24} color={COLORS.secondary} />
          </View>
          <View style={styles.titleContainer}>
            <Text style={styles.noticeTitle}>{notice.notice_title}</Text>
            <Text style={styles.timeAgo}>{getTimeAgo(notice.notice_date)}</Text>
          </View>
        </View>

        <View style={styles.noticeBody}>
          {expanded ? (
            <RenderHtml
              contentWidth={htmlConfig.width}
              source={{ html: notice.notice_details }}
              tagsStyles={tagsStyles}
            />
          ) : (
            <Text style={styles.noticePreview} numberOfLines={3}>
              {previewText}
            </Text>
          )}
        </View>

        {/* Attachment */}
        {notice.notice_attachment && (
          <TouchableOpacity
            style={styles.attachmentButton}
            onPress={() => handleAttachment(notice.notice_attachment)}
          >
            <Ionicons name="attach" size={18} color={COLORS.secondary} />
            <Text style={styles.attachmentText}>View Attachment</Text>
          </TouchableOpacity>
        )}

        {/* Read More Button */}
        {shouldShowMore && (
          <TouchableOpacity
            style={styles.readMoreButton}
            onPress={() => setExpanded(!expanded)}
          >
            <Text style={styles.readMoreText}>
              {expanded ? 'Show Less' : 'Read More'}
            </Text>
            <Ionicons
              name={expanded ? 'chevron-up' : 'chevron-down'}
              size={16}
              color={COLORS.secondary}
            />
          </TouchableOpacity>
        )}
      </View>
    </TouchableOpacity>
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
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.white,
    marginHorizontal: 20,
    paddingHorizontal: 15,
    borderRadius: 15,
    height: 50,
  },
  searchInput: {
    flex: 1,
    marginLeft: 10,
    fontSize: 15,
    color: COLORS.primary,
  },
  filterContainer: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingVertical: 15,
    gap: 10,
  },
  filterButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    paddingHorizontal: 12,
    borderRadius: 12,
    backgroundColor: COLORS.white,
    gap: 6,
  },
  filterButtonActive: {
    backgroundColor: COLORS.secondary,
  },
  filterButtonText: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.gray,
  },
  filterButtonTextActive: {
    color: COLORS.white,
  },
  countBadge: {
    backgroundColor: COLORS.lightGray,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 10,
  },
  countBadgeActive: {
    backgroundColor: 'rgba(255, 255, 255, 0.3)',
  },
  countText: {
    fontSize: 11,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  countTextActive: {
    color: COLORS.white,
  },
  errorBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFEBEE',
    padding: 12,
    marginHorizontal: 20,
    marginTop: 10,
    borderRadius: 10,
    gap: 10,
  },
  errorBannerText: {
    flex: 1,
    fontSize: 13,
    color: COLORS.error,
  },
  scrollView: {
    flex: 1,
    paddingHorizontal: 20,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 60,
  },
  emptyText: {
    fontSize: 16,
    color: COLORS.gray,
    marginTop: 20,
    textAlign: 'center',
  },
  noticeCard: {
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
  dateBadge: {
    alignSelf: 'flex-start',
    marginLeft: 15,
    marginTop: 15,
    borderRadius: 20,
    overflow: 'hidden',
  },
  dateBadgeGradient: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 6,
    gap: 6,
  },
  dateText: {
    fontSize: 12,
    fontWeight: 'bold',
    color: COLORS.primary,
  },
  noticeContent: {
    padding: 15,
  },
  noticeHeader: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 12,
  },
  iconContainer: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: COLORS.secondary + '20',
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  titleContainer: {
    flex: 1,
  },
  noticeTitle: {
    fontSize: 17,
    fontWeight: 'bold',
    color: COLORS.primary,
    marginBottom: 4,
  },
  timeAgo: {
    fontSize: 12,
    color: COLORS.gray,
  },
  noticeBody: {
    marginTop: 8,
  },
  noticePreview: {
    fontSize: 14,
    color: COLORS.gray,
    lineHeight: 22,
  },
  attachmentButton: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: COLORS.secondary + '15',
    paddingHorizontal: 15,
    paddingVertical: 10,
    borderRadius: 10,
    marginTop: 12,
    alignSelf: 'flex-start',
  },
  attachmentText: {
    fontSize: 13,
    fontWeight: '600',
    color: COLORS.secondary,
    marginLeft: 8,
  },
  readMoreButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 12,
    paddingVertical: 8,
  },
  readMoreText: {
    fontSize: 14,
    fontWeight: '600',
    color: COLORS.secondary,
    marginRight: 4,
  },
});