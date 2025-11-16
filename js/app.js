// App State
let currentUser = null;
let currentSection = 'feed';

// Initialize app
document.addEventListener('DOMContentLoaded', () => {
    initializeApp();
});

function initializeApp() {
    // Check if user is logged in
    const user = localStorage.getItem('user');
    if (user) {
        currentUser = JSON.parse(user);
        showDashboard();
    } else {
        showPage('loginPage');
    }

    // Event listeners
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    document.getElementById('registerForm').addEventListener('submit', handleRegister);
    document.getElementById('showRegister').addEventListener('click', (e) => {
        e.preventDefault();
        showPage('registerPage');
    });
    document.getElementById('showLogin').addEventListener('click', (e) => {
        e.preventDefault();
        showPage('loginPage');
    });
    document.getElementById('logoutBtn').addEventListener('click', handleLogout);
    document.getElementById('createPostBtn').addEventListener('click', createPost);

    // Filters
    document.getElementById('feedTypeFilter')?.addEventListener('change', loadFeed);
    document.getElementById('feedCategoryFilter')?.addEventListener('change', loadFeed);
    document.getElementById('eventCategoryFilter')?.addEventListener('change', loadEvents);
    document.getElementById('discussionTopicFilter')?.addEventListener('change', loadDiscussions);
    document.getElementById('resourceCategoryFilter')?.addEventListener('change', loadResources);
    document.getElementById('resourceTypeFilter')?.addEventListener('change', loadResources);

    // Navigation
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const section = e.target.dataset.section;
            showSection(section);
        });
    });
}

function showPage(pageId) {
    document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
    document.getElementById(pageId).classList.add('active');
}

function showSection(sectionId) {
    currentSection = sectionId;
    document.querySelectorAll('.section').forEach(section => section.classList.remove('active'));
    document.getElementById(sectionId + 'Section').classList.add('active');
    
    // Load section data
    if (sectionId === 'feed') loadFeed();
    if (sectionId === 'events') loadEvents();
    if (sectionId === 'discussions') loadDiscussions();
    if (sectionId === 'resources') loadResources();
    if (sectionId === 'profile') loadProfile();
}

// Authentication
async function handleLogin(e) {
    e.preventDefault();
    const email = document.getElementById('loginEmail').value;
    const password = document.getElementById('loginPassword').value;

    try {
        const response = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'login', email, password })
        });
        const data = await response.json();
        
        if (data.success) {
            currentUser = data.user;
            localStorage.setItem('user', JSON.stringify(data.user));
            showDashboard();
        } else {
            alert(data.message || 'Login failed');
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('Login failed. Please try again.');
    }
}

async function handleRegister(e) {
    e.preventDefault();
    const name = document.getElementById('regName').value;
    const email = document.getElementById('regEmail').value;
    const password = document.getElementById('regPassword').value;
    const role = document.getElementById('regRole').value;

    try {
        const response = await fetch('api/auth.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'register', name, email, password, role })
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Registration successful! Please login.');
            showPage('loginPage');
        } else {
            alert(data.message || 'Registration failed');
        }
    } catch (error) {
        console.error('Registration error:', error);
        alert('Registration failed. Please try again.');
    }
}

function handleLogout() {
    localStorage.removeItem('user');
    currentUser = null;
    showPage('loginPage');
}

function showDashboard() {
    document.getElementById('userName').textContent = currentUser.name;
    showPage('dashboardPage');
    loadFeed();
}

// Feed Functions
async function loadFeed() {
    try {
        const typeFilter = document.getElementById('feedTypeFilter')?.value || 'all';
        const categoryFilter = document.getElementById('feedCategoryFilter')?.value || 'all';
        
        const response = await fetch(`api/posts.php?type=${typeFilter}&category=${categoryFilter}`);
        const posts = await response.json();
        displayFeed(posts);
    } catch (error) {
        console.error('Error loading feed:', error);
    }
}

function displayFeed(posts) {
    const feedList = document.getElementById('feedList');
    feedList.innerHTML = posts.map(post => `
        <div class="feed-item">
            <div class="author">
                ${post.author_name} 
                <span class="badge ${post.author_role}">${post.author_role}</span>
                ${post.post_type !== 'post' ? `<span class="badge ${post.post_type}">${post.post_type}</span>` : ''}
            </div>
            <div class="category-tag">${post.category}</div>
            <div class="content">${post.content}</div>
            <div class="meta">
                <span onclick="likePost(${post.id})">👍 ${post.likes_count} Likes</span>
                <span>💬 ${post.comments_count} Comments</span>
                <span>${new Date(post.created_at).toLocaleDateString()}</span>
            </div>
        </div>
    `).join('');
}

async function createPost() {
    const content = document.getElementById('postContent').value;
    const post_type = document.getElementById('postType').value;
    const category = document.getElementById('postCategory').value;
    
    if (!content.trim()) {
        alert('Please enter some content');
        return;
    }

    try {
        const response = await fetch('api/posts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ content, post_type, category })
        });
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('postContent').value = '';
            loadFeed();
        } else {
            alert('Failed to create post');
        }
    } catch (error) {
        console.error('Error creating post:', error);
    }
}

async function likePost(postId) {
    // Placeholder for like functionality
    alert('Like feature - to be implemented with post_likes table');
}

// Events Functions
async function loadEvents() {
    try {
        const categoryFilter = document.getElementById('eventCategoryFilter')?.value || 'all';
        const response = await fetch(`api/events.php?category=${categoryFilter}`);
        const events = await response.json();
        displayEvents(events);
    } catch (error) {
        console.error('Error loading events:', error);
    }
}

function displayEvents(events) {
    const eventsList = document.getElementById('eventsList');
    eventsList.innerHTML = events.map(event => `
        <div class="event-item">
            <h3>${event.title}</h3>
            <span class="badge ${event.category}">${event.category}</span>
            <span class="badge ${event.status}">${event.status}</span>
            <div class="event-meta">
                📅 ${new Date(event.event_date).toLocaleString()} | 📍 ${event.location}
            </div>
            <p>${event.description}</p>
            <div class="event-stats">
                <span>👥 ${event.registered_count} registered</span>
                ${event.avg_rating ? `<span>⭐ ${parseFloat(event.avg_rating).toFixed(1)}/5</span>` : ''}
            </div>
            <button onclick="registerForEvent(${event.id})">Register</button>
            ${currentUser.role !== 'student' ? `<button onclick="viewEventDetails(${event.id})">Manage</button>` : ''}
        </div>
    `).join('');
}

async function registerForEvent(eventId) {
    try {
        const response = await fetch('api/event_registration.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'register', event_id: eventId })
        });
        const data = await response.json();
        alert(data.message);
        if (data.success) loadEvents();
    } catch (error) {
        console.error('Error registering:', error);
    }
}

function viewEventDetails(eventId) {
    alert('Event management panel - View registrations, mark attendance, generate certificates');
}

// Discussions Functions
async function loadDiscussions() {
    try {
        const topicFilter = document.getElementById('discussionTopicFilter')?.value || 'all';
        const response = await fetch(`api/discussions.php?topic=${topicFilter}`);
        const discussions = await response.json();
        displayDiscussions(discussions);
    } catch (error) {
        console.error('Error loading discussions:', error);
    }
}

function displayDiscussions(discussions) {
    const discussionsList = document.getElementById('discussionsList');
    discussionsList.innerHTML = discussions.map(disc => `
        <div class="feed-item">
            <h3>${disc.title}</h3>
            <span class="badge ${disc.topic}">${disc.topic}</span>
            ${disc.is_locked ? '<span class="badge locked">🔒 Locked</span>' : ''}
            <div class="author">${disc.author_name}</div>
            <div class="content">${disc.content}</div>
            <div class="meta">
                <span>💬 ${disc.replies_count} Replies</span>
                <span>${new Date(disc.created_at).toLocaleDateString()}</span>
            </div>
        </div>
    `).join('');
}

// Resources Functions
async function loadResources() {
    try {
        const categoryFilter = document.getElementById('resourceCategoryFilter')?.value || 'all';
        const typeFilter = document.getElementById('resourceTypeFilter')?.value || 'all';
        const response = await fetch(`api/resources.php?category=${categoryFilter}&type=${typeFilter}`);
        const resources = await response.json();
        displayResources(resources);
    } catch (error) {
        console.error('Error loading resources:', error);
    }
}

function displayResources(resources) {
    const resourcesList = document.getElementById('resourcesList');
    resourcesList.innerHTML = resources.map(res => `
        <div class="feed-item">
            <h3>${res.title}</h3>
            <span class="badge ${res.category}">${res.category}</span>
            <span class="badge ${res.file_type}">${res.file_type}</span>
            <p>${res.description || ''}</p>
            <div class="meta">
                <span>Uploaded by: ${res.uploader_name}</span>
                <span>📥 ${res.download_count} downloads</span>
                <span>${new Date(res.created_at).toLocaleDateString()}</span>
            </div>
            <button onclick="downloadResource(${res.id})">Download</button>
        </div>
    `).join('');
}

function downloadResource(resourceId) {
    alert('Download functionality - file will be downloaded');
}

// Profile Functions
function loadProfile() {
    const profileContent = document.getElementById('profileContent');
    profileContent.innerHTML = `
        <div class="profile-card">
            <h3>${currentUser.name}</h3>
            <p>Email: ${currentUser.email}</p>
            <p>Role: ${currentUser.role}</p>
            <button onclick="editProfile()">Edit Profile</button>
        </div>
    `;
}

function editProfile() {
    alert('Profile editing form - Update name, photo, department, etc.');
}

// Modal Functions
function openModal(modalId) {
    document.getElementById(modalId).style.display = 'block';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.style.display = 'none';
    }
}

// Event Creation
document.getElementById('createEventBtn')?.addEventListener('click', () => {
    if (currentUser.role === 'student') {
        alert('Only Faculty and Club Admins can create events');
        return;
    }
    openModal('createEventModal');
});

document.getElementById('createEventForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const eventData = {
        title: document.getElementById('eventTitle').value,
        description: document.getElementById('eventDescription').value,
        event_date: document.getElementById('eventDate').value,
        location: document.getElementById('eventLocation').value,
        category: document.getElementById('eventCategory').value,
        max_participants: document.getElementById('eventMaxParticipants').value || 0,
        requires_approval: document.getElementById('eventRequiresApproval').checked
    };

    try {
        const response = await fetch('api/events.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(eventData)
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Event created successfully!');
            closeModal('createEventModal');
            document.getElementById('createEventForm').reset();
            loadEvents();
        } else {
            alert('Failed to create event');
        }
    } catch (error) {
        console.error('Error creating event:', error);
    }
});

// Discussion Creation
document.getElementById('createDiscussionBtn')?.addEventListener('click', () => {
    openModal('createDiscussionModal');
});

document.getElementById('createDiscussionForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const discussionData = {
        title: document.getElementById('discussionTitle').value,
        topic: document.getElementById('discussionTopic').value,
        content: document.getElementById('discussionContent').value
    };

    try {
        const response = await fetch('api/discussions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(discussionData)
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Discussion created successfully!');
            closeModal('createDiscussionModal');
            document.getElementById('createDiscussionForm').reset();
            loadDiscussions();
        } else {
            alert('Failed to create discussion');
        }
    } catch (error) {
        console.error('Error creating discussion:', error);
    }
});

// Resource Upload
document.getElementById('uploadResourceBtn')?.addEventListener('click', () => {
    if (currentUser.role === 'student') {
        alert('Only Faculty and Club Admins can upload resources');
        return;
    }
    openModal('uploadResourceModal');
});

document.getElementById('uploadResourceForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const resourceData = {
        title: document.getElementById('resourceTitle').value,
        description: document.getElementById('resourceDescription').value,
        category: document.getElementById('resourceCategory').value,
        file_type: document.getElementById('resourceType').value,
        file_path: '/uploads/' + document.getElementById('resourceFile').files[0]?.name || 'placeholder.pdf',
        allowed_roles: document.getElementById('resourceAllowedRoles').value || 'all'
    };

    try {
        const response = await fetch('api/resources.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(resourceData)
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Resource uploaded successfully!');
            closeModal('uploadResourceModal');
            document.getElementById('uploadResourceForm').reset();
            loadResources();
        } else {
            alert('Failed to upload resource');
        }
    } catch (error) {
        console.error('Error uploading resource:', error);
    }
});

// Feedback Functions
let currentFeedbackRating = 5;

function setRating(rating) {
    currentFeedbackRating = rating;
    document.getElementById('feedbackRating').value = rating;
    
    // Visual feedback
    const stars = document.querySelectorAll('.rating-stars span');
    stars.forEach((star, index) => {
        star.style.opacity = index < rating ? '1' : '0.3';
    });
}

function openFeedbackModal(eventId) {
    document.getElementById('feedbackEventId').value = eventId;
    openModal('feedbackModal');
}

document.getElementById('feedbackForm')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const feedbackData = {
        event_id: document.getElementById('feedbackEventId').value,
        rating: currentFeedbackRating,
        comment: document.getElementById('feedbackComment').value
    };

    try {
        const response = await fetch('api/feedback.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(feedbackData)
        });
        const data = await response.json();
        
        if (data.success) {
            alert('Feedback submitted successfully!');
            closeModal('feedbackModal');
            document.getElementById('feedbackForm').reset();
            setRating(5);
        } else {
            alert('Failed to submit feedback');
        }
    } catch (error) {
        console.error('Error submitting feedback:', error);
    }
});
