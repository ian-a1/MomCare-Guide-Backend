// Main MomCare App JavaScript with PHP API integration
class MomCareApp {
  constructor() {
    this.currentTab = "home";
    this.userSession = null;
    this.profileData = null;
    this.currentPostId = null;
    this.init();
  }

  async init() {
    await this.checkAuthentication();
    this.setupEventListeners();
    this.switchTab("home", true); // Initial load
  }

  async checkAuthentication() {
    const session = localStorage.getItem("user_session");
    if (!session) {
      window.location.href = "landing.html";
      return;
    }
    this.userSession = JSON.parse(session);
    if (!this.userSession.session_token) {
        // If the token is missing, the user is not properly logged in.
        localStorage.removeItem("user_session");
        window.location.href = "landing.html";
    }
  }

  // --- DATA FETCHING --- //
  async apiCall(endpoint, method = 'GET', body = null) {
    const options = {
      method,
      headers: { 
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${this.userSession?.session_token}`
      },
    };
    if (body) {
      options.body = JSON.stringify(body);
    }
    try {
      // FIX: Ensure API path is relative to index.html
      const response = await fetch(`api/${endpoint}`, options);
      
      if (response.status === 41) { // Unauthorized
          this.showNotification('Session expired. Please log in again.', 'error');
          this.signOut(true); // Force sign out without confirmation
          return { success: false, message: 'Unauthorized' };
      }

      const result = await response.json();
      if (!response.ok && !result.success) {
         throw new Error(result.message || `HTTP error! status: ${response.status}`);
      }
      return result;

    } catch (error) {
      console.error(`API call failed for ${endpoint}:`, error);
      this.showNotification('An error occurred. Please try again.', 'error');
      return { success: false, message: error.message };
    }
  }

  async loadUserProfile() {
    const result = await this.apiCall('profile.php');
    if (result.success) {
      this.profileData = result.profile;
      this.updateProfileUI();
    }
  }

  async loadHomeData() {
      this.loadAppointments();
      this.loadMilestones();
  }

  async loadAppointments() {
    const result = await this.apiCall('appointments.php');
    if (result.success) {
        this.displayAppointments(result.appointments.slice(0, 3));
        this.displayNextAppointment(result.appointments);
    }
  }

  async loadMilestones() {
    const result = await this.apiCall('milestones.php');
    if(result.success && result.milestones) {
        this.displayMilestones(result.milestones.slice(0, 3));
        
        // --- MODIFIED: Logic to update progress bar based on milestones ---
        const allMilestones = result.milestones;
        const completedMilestones = allMilestones.filter(m => m.status === 'complete').length;
        const totalMilestones = allMilestones.length;
        
        this.updateMilestoneProgress(completedMilestones, totalMilestones);
    } else {
        // Handle case with no milestones
        this.updateMilestoneProgress(0, 0);
    }
  }
  
  async loadEmergencyContacts() {
    const result = await this.apiCall('emergency_contacts.php');
    if(result.success) {
        this.displayEmergencyContacts(result.contacts);
    }
  }
  
  async loadLibraryContent(category = 'all', search = '') {
    const result = await this.apiCall(`library.php?category=${category}&search=${encodeURIComponent(search)}`);
    if(result.success) {
        this.displayLibraryItems(result.content);
    }
  }
  
  async loadDownloadsList() {
    const result = await this.apiCall('downloads.php');
    if(result.success) {
        this.displayDownloadsList(result.grouped);
    }
  }

  async loadForumStats() {
    const result = await this.apiCall('forum_stats.php');
    if (result.success && result.stats) {
        document.getElementById("activeMembers").textContent = `${result.stats.active_members} active members`;
        document.getElementById("postCount").textContent = `${result.stats.posts_today} new posts today`;
    }
  }
  
  async loadForumContent(tag = 'all', search = '') {
      let url = `forum.php?action=posts&tag=${tag}&search=${encodeURIComponent(search)}`;
      const result = await this.apiCall(url);
      if (result.success) {
          this.displayForumPosts(result.posts);
      }
  }

  // --- UI UPDATING --- //
  updateProfileUI() {
    if (!this.profileData) return;
    const { name, current_week } = this.profileData;
    document.getElementById("userName").textContent = name || "User";
    document.getElementById("displayName").textContent = name || "Your Name";
    document.getElementById("avatarLetter").textContent = (name || "U").charAt(0).toUpperCase();
    document.getElementById("currentWeek").textContent = current_week || 0;
    // MODIFIED: The progress bar is now handled by updateMilestoneProgress to avoid conflicting updates.
  }

  /**
   * MODIFIED: This function updates the progress bar based on milestone completion and sets its color.
   * @param {number} completed - The number of completed milestones.
   * @param {number} total - The total number of milestones.
   */
  updateMilestoneProgress(completed, total) {
    const progressLabel = document.querySelector('.progress-label');
    const progressWeeks = document.getElementById('progressWeeks');
    const progressBar = document.getElementById('progressBar');
    
    if (total > 0) {
        const percentage = (completed / total) * 100;
        progressLabel.textContent = 'Milestones';
        progressWeeks.textContent = `${completed}/${total} completed`;
        progressBar.style.width = `${percentage}%`;
        progressBar.style.backgroundColor = '#27ae60'; // Green color for progress
    } else {
        progressLabel.textContent = 'Milestones';
        progressWeeks.textContent = 'No milestones yet';
        progressBar.style.width = '0%';
        progressBar.style.backgroundColor = '#fff'; // Revert to default color
    }
  }
  
  displayAppointments(appointments, containerId = "appointmentsList") {
    const container = document.getElementById(containerId);
    container.innerHTML = "";
    if (!appointments || appointments.length === 0) {
        container.innerHTML = "<p class='no-data'>No appointments scheduled</p>";
        return;
    }
    appointments.forEach(apt => {
        const date = new Date(apt.appointment_date);
        container.innerHTML += `
            <div class="appointment-item">
                <div class="appointment-item-info">
                    <div class="appointment-item-name">${apt.title}</div>
                    <div class="appointment-item-date">${date.toLocaleString()}</div>
                    <div class="appointment-item-doctor">${apt.doctor_name || 'N/A'}</div>
                </div>
            </div>`;
    });
  }

  /**
   * MODIFIED: This function now correctly finds and displays the next upcoming appointment.
   * It uses a more reliable date parsing method and displays clearer information.
   * @param {Array} appointments - The list of all user appointments.
   */
  displayNextAppointment(appointments) {
    if (!appointments) return;

    // FIX: Make date parsing more reliable by replacing space with 'T' for ISO 8601 format.
    const upcoming = appointments.filter(apt => {
        const aptDate = new Date(apt.appointment_date.replace(' ', 'T'));
        return aptDate > new Date();
    }).sort((a, b) => new Date(a.appointment_date.replace(' ', 'T')) - new Date(b.appointment_date.replace(' ', 'T')));
    
    const nextApt = upcoming[0];
    
    const appointmentTypeEl = document.getElementById("nextAppointmentType");
    const appointmentDateEl = document.getElementById("nextAppointmentDate");
    const appointmentDoctorEl = document.getElementById("nextAppointmentDoctor");

    if (nextApt) {
        appointmentTypeEl.textContent = nextApt.type ? nextApt.type.charAt(0).toUpperCase() + nextApt.type.slice(1) : 'Appointment';
        
        // IMPROVEMENT: Display the appointment title and a more readable date/time.
        appointmentDateEl.textContent = nextApt.title;
        appointmentDoctorEl.textContent = new Date(nextApt.appointment_date.replace(' ', 'T')).toLocaleString('en-US', {
            month: 'long', day: 'numeric', year: 'numeric', hour: '2-digit', minute: '2-digit'
        });
    } else {
        appointmentTypeEl.textContent = "No upcoming appointments";
        appointmentDateEl.textContent = "Add one to see it here!";
        appointmentDoctorEl.textContent = "";
    }
  }

  displayMilestones(milestones, containerId = "milestonesList") {
    const container = document.getElementById(containerId);
    container.innerHTML = "";
    if (!milestones || milestones.length === 0) {
        container.innerHTML = "<p class='no-data'>No milestones recorded</p>";
        return;
    }
    milestones.forEach(m => {
        container.innerHTML += `
            <div class="milestone-item">
                <div class="milestone-icon">${m.status === "complete" ? "✓" : "○"}</div>
                <div class="milestone-info">
                    <div class="milestone-name">${m.name}</div>
                    <div class="milestone-week">Week ${m.week_number}</div>
                </div>
                <div class="milestone-status status-${m.status}">${m.status}</div>
            </div>`;
    });
  }
  
  displayEmergencyContacts(contacts) {
    const list = document.getElementById('emergencyContactsList');
    list.innerHTML = '';
    if (!contacts || contacts.length === 0) {
        list.innerHTML = "<p class='no-data'>No contacts added.</p>";
        return;
    }
    contacts.forEach(c => {
        list.innerHTML += `
            <div class="contact-item">
                <div class="contact-info">
                    <div class="contact-name">${c.name} (${c.relationship})</div>
                    <div class="contact-phone">${c.phone}</div>
                </div>
                <div class="contact-actions">
                    <a class="btn btn-call" href="tel:${c.phone}">Call</a>
                    <button class="btn btn-delete" data-id="${c.id}">Delete</button>
                </div>
            </div>`;
    });
    // Add event listeners for new buttons
    list.querySelectorAll('.btn-delete').forEach(btn => btn.addEventListener('click', e => this.deleteEmergencyContact(e.target.dataset.id)));
  }

  displayForumPosts(posts) {
    const container = document.getElementById('postsContainer');
    container.innerHTML = '';
    if (!posts || posts.length === 0) {
        container.innerHTML = '<p class="no-data">No posts found.</p>';
        return;
    }
    posts.forEach(post => {
        container.innerHTML += `
            <div class="post-item" data-post-id="${post.id}">
                <h4>${post.title}</h4>
                <p>by ${post.author_name} on ${new Date(post.created_at).toLocaleDateString()}</p>
                <div>
                    <button class="like-btn" data-post-id="${post.id}">👍 ${post.likes_count || 0}</button>
                    <span>💬 ${post.replies_count || 0} Replies</span>
                </div>
            </div>`;
    });
    container.querySelectorAll('.post-item').forEach(item => item.addEventListener('click', e => {
        if (!e.target.classList.contains('like-btn')) {
            this.viewPost(item.dataset.postId);
        }
    }));
    container.querySelectorAll('.like-btn').forEach(btn => btn.addEventListener('click', e => {
        e.stopPropagation();
        this.likePost(btn.dataset.postId);
    }));
  }
  
  displayLibraryItems(items) {
      const container = document.getElementById('library-content-area');
      container.innerHTML = '';
      if (!items || items.length === 0) {
          container.innerHTML = '<p class="no-data">No items found in the library.</p>';
          return;
      }
      
      const iconMap = {
          'recipes': '🍲',
          'mental-health': '🧘',
          'exercise': '🏃‍♀️'
      };

      items.forEach(item => {
          container.innerHTML += `
            <div class="category-card">
                <div class="category-icon">${iconMap[item.category] || '📚'}</div>
                <div class="category-info">
                    <div class="category-name">${item.title}</div>
                    <div class="category-description">${item.description}</div>
                    <div class="category-stats">
                        <span class="stat-item">⭐ ${item.rating || 0}</span>
                        <span class="stat-item">👁️ ${item.views_count || 0}</span>
                    </div>
                </div>
                 <button class="category-view-btn" data-id="${item.id}">View</button>
            </div>
          `;
      });
  }
  
  displayDownloadsList(groupedDownloads) {
      const container = document.getElementById('download-content-area');
      container.innerHTML = '';
      if (!groupedDownloads || Object.keys(groupedDownloads).length === 0) {
          container.innerHTML = '<p class="no-data">You have no downloaded items.</p>';
          return;
      }
      
      for(const category in groupedDownloads) {
          let itemsHtml = groupedDownloads[category].map(item => `
            <div class="download-item">
                <div class="download-info">
                    <div class="download-name">${item.title}</div>
                    <div class="download-description">${(item.file_size_mb || 0)} MB</div>
                </div>
                <button class="btn-delete" data-id="${item.id}">Delete</button>
            </div>
          `).join('');
          
          container.innerHTML += `
            <div class="download-section">
                <div class="download-section-header">
                    <h3 class="download-section-title">${category.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase())}</h3>
                </div>
                ${itemsHtml}
            </div>
          `;
      }
  }

  async viewPost(postId) {
    this.currentPostId = postId;
    const result = await this.apiCall(`forum.php?action=post_details&post_id=${postId}`);
    if (result.success) {
        const { post, replies } = result;
        const modalContent = document.getElementById('viewPostContent');
        
        let repliesHtml = (replies || []).map(reply => `
            <div class="reply-item">
                <p>${reply.content}</p>
                <small>by ${reply.author_name} on ${new Date(reply.created_at).toLocaleDateString()}</small>
            </div>
        `).join('');

        modalContent.innerHTML = `
            <h3>${post.title}</h3>
            <p>${post.content}</p>
            <small>by ${post.author_name} on ${new Date(post.created_at).toLocaleDateString()}</small>
            <hr>
            <h4>Replies</h4>
            <div id="repliesContainer">${repliesHtml || '<p class="no-data">No replies yet.</p>'}</div>
        `;
        document.getElementById('viewPostTitle').textContent = post.title;
        this.openModal('viewPostModal');
    }
  }


  // --- EVENT LISTENERS & HANDLERS --- //
  setupEventListeners() {
    // Tab Navigation
    document.querySelectorAll(".nav-item").forEach(item => {
      item.addEventListener("click", () => this.switchTab(item.dataset.tab));
    });

    // Modals
    document.querySelectorAll(".modal-close, .btn-cancel").forEach(btn => {
      btn.addEventListener("click", e => this.closeModal(e.target.closest(".modal-overlay").id));
    });
    document.getElementById('confirmCancel').addEventListener('click', () => this.closeModal('confirmModal'));
    
    // Guidelines Agreement
    document.getElementById('agreeGuidelinesBtn').addEventListener('click', () => {
        localStorage.setItem('forumGuidelinesAgreed', 'true');
        this.closeModal('forumGuidelinesModal');
        // Now, manually trigger the tab switch to the forum
        this.switchTab('chat');
    });

    // Home Tab Buttons
    document.getElementById('addAppointmentBtn').addEventListener('click', () => this.openModal('appointmentModal'));
    document.getElementById('addMilestoneBtn').addEventListener('click', () => this.openModal('milestoneModal'));
    document.querySelectorAll('.view-all').forEach(el => el.addEventListener('click', e => this.viewAll(e.target.dataset.type)));
    
    // Emergency Tab
    document.getElementById('sosButton').addEventListener('click', () => {
        document.getElementById('sosStatus').style.display = 'block';
        setTimeout(() => document.getElementById('sosStatus').style.display = 'none', 5000);
        this.showNotification('SOS Sent!', 'success');
    });
    document.getElementById('addEmergencyContactBtn').addEventListener('click', () => this.openModal('addContactModal'));

    // Library Tab
    document.getElementById('librarySearchInput').addEventListener('input', e => this.loadLibraryContent('all', e.target.value));
    document.querySelector('.library-category-tabs').addEventListener('click', e => {
        if(e.target.classList.contains('category-tab')) {
            document.querySelector('.library-category-tabs .category-tab.active').classList.remove('active');
            e.target.classList.add('active');
            this.loadLibraryContent(e.target.dataset.category);
        }
    });

    // Forum Tab
    document.getElementById('createPostBtn').addEventListener('click', () => this.openModal('createPostModal'));
    document.getElementById('savePostBtn').addEventListener('click', () => this.savePost());
    document.getElementById('replyForm').addEventListener('submit', e => {
        e.preventDefault();
        this.saveReply();
    });
    document.getElementById('searchBar').addEventListener('input', e => this.loadForumContent('all', e.target.value));
    document.getElementById('trendingTags').addEventListener('click', e => {
        if(e.target.classList.contains('tag')) {
            document.querySelector('#trendingTags .tag.active').classList.remove('active');
            e.target.classList.add('active');
            this.loadForumContent(e.target.dataset.tag);
        }
    });

    // Profile Tab
    document.querySelectorAll('.profile-menu-item').forEach(item => {
      item.addEventListener('click', () => this.handleProfileMenu(item.dataset.section));
    });
    document.querySelectorAll('#basicInfoModal .btn-save, #medicalInfoModal .btn-save, #privacyInfoModal .btn-save').forEach(btn => {
        btn.addEventListener('click', e => this.saveProfileSection(e.target.dataset.section));
    });

    // Modal Forms
    document.getElementById('saveAppointment').addEventListener('click', () => this.saveForm('appointmentForm', 'appointments.php', this.loadAppointments.bind(this)));
    document.getElementById('saveMilestone').addEventListener('click', () => this.saveForm('milestoneForm', 'milestones.php', this.loadMilestones.bind(this)));
    document.getElementById('saveContact').addEventListener('click', () => this.saveForm('emergencyContactForm', 'emergency_contacts.php', this.loadEmergencyContacts.bind(this)));
  }
  
  async saveForm(formId, endpoint, callback) {
    const form = document.getElementById(formId);
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    const result = await this.apiCall(endpoint, 'POST', data);
    
    if (result.success) {
        this.showNotification(result.message || 'Saved successfully!', 'success');
        this.closeModal(form.closest('.modal-overlay').id);
        form.reset();
        if(callback) callback();
    } else {
        this.showNotification(result.message || 'Failed to save.', 'error');
    }
  }

  async deleteEmergencyContact(id) {
    this.showConfirmation('Are you sure you want to delete this contact?', async () => {
        const result = await this.apiCall(`emergency_contacts.php?id=${id}`, 'DELETE');
        if (result.success) {
            this.showNotification('Contact deleted', 'success');
            this.loadEmergencyContacts();
        }
    });
  }

  handleProfileMenu(section) {
    switch (section) {
      case 'signOut':
        this.signOut();
        break;
      case 'help-support':
        this.openModal('helpSupportModal');
        break;
      case 'basic-info':
      case 'medical-info':
      case 'privacy-info':
        this.openProfileSection(section);
        break;
    }
  }

  openProfileSection(section) {
    const modalId = `${section.replace(/-(\w)/g, (match, letter) => letter.toUpperCase())}Modal`;
    const formId = `${section.replace(/-(\w)/g, (match, letter) => letter.toUpperCase())}Form`;
    const form = document.getElementById(formId);
    
    // Populate form
    for(const key in this.profileData) {
        const input = form.elements[key];
        if(input) {
            if(input.type === 'checkbox') {
                input.checked = !!parseInt(this.profileData[key]);
            } else {
                input.value = this.profileData[key] || '';
            }
        }
    }
    this.openModal(modalId);
  }

  
  async saveProfileSection(section) {
    const formId = `${section.replace(/-(\w)/g, (match, letter) => letter.toUpperCase())}Form`;
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    // Handle checkboxes correctly for privacy-info
    if(section === 'privacy-info') {
        data.notification_appointments = form.elements.notification_appointments.checked ? 1 : 0;
        data.notification_milestones = form.elements.notification_milestones.checked ? 1 : 0;
        data.notification_forum = form.elements.notification_forum.checked ? 1 : 0;
        data.privacy_profile_public = form.elements.privacy_profile_public.checked ? 1 : 0;
    }

    const payload = { section, data };
    const result = await this.apiCall('profile.php', 'PUT', payload);

    if(result.success) {
        this.showNotification('Profile updated!', 'success');
        this.closeModal(`${section.replace(/-(\w)/g, (match, letter) => letter.toUpperCase())}Modal`);
        await this.loadUserProfile(); // Refresh profile data
    }
  }

  async viewAll(type) {
    const result = await this.apiCall(`${type}.php`);
    if(result.success) {
        document.getElementById('viewAllTitle').textContent = `All ${type.charAt(0).toUpperCase() + type.slice(1)}`;
        if (type === 'appointments') {
            this.displayAppointments(result.appointments, 'viewAllBody');
        } else if (type === 'milestones') {
            this.displayMilestones(result.milestones, 'viewAllBody');
        }
        this.openModal('viewAllModal');
    }
  }

  async savePost() {
      const form = document.getElementById('createPostForm');
       if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());

      const result = await this.apiCall('forum.php?action=create_post', 'POST', data);
      if(result.success) {
          this.showNotification('Post created!', 'success');
          this.closeModal('createPostModal');
          form.reset();
          this.loadForumContent(); // Refresh forum
      }
  }

  async saveReply() {
      const form = document.getElementById('replyForm');
      if (!form.checkValidity()) {
        form.reportValidity();
        return;
      }
      const formData = new FormData(form);
      const data = Object.fromEntries(formData.entries());
      data.post_id = this.currentPostId;

      const result = await this.apiCall('forum.php?action=create_reply', 'POST', data);
      if(result.success) {
          form.reset();
          this.viewPost(this.currentPostId); // Refresh post view
      }
  }

  async likePost(postId) {
      const result = await this.apiCall('forum.php?action=like_post', 'POST', { post_id: postId });
      if(result.success) {
          // Optimistically update UI or reload forum
          this.loadForumContent(document.querySelector('#trendingTags .tag.active').dataset.tag);
      }
  }

  signOut(force = false) {
    const performSignOut = () => {
        localStorage.removeItem("user_session");
        this.apiCall('auth.php?action=logout', 'POST'); // Clear server session
        window.location.href = "landing.html";
    };

    if (force) {
        performSignOut();
    } else {
        this.showConfirmation("Are you sure you want to sign out?", performSignOut);
    }
  }
  
  switchTab(tab, isInitialLoad = false) {
    if (this.currentTab === tab && !isInitialLoad) return;
    
    // Intercept Forum Tab to check for guidelines agreement
    if (tab === 'chat' && !isInitialLoad) {
        const agreed = localStorage.getItem('forumGuidelinesAgreed');
        if (agreed !== 'true') {
            this.openModal('forumGuidelinesModal');
            // Stop the tab switch until user agrees
            return;
        }
    }

    document.querySelectorAll(".tab-content").forEach(c => c.style.display = "none");
    document.querySelectorAll(".nav-item").forEach(i => i.classList.remove("active"));

    document.getElementById(`tab-${tab}`).style.display = "block";
    document.querySelector(`.nav-item[data-tab="${tab}"]`).classList.add("active");
    this.currentTab = tab;
    
    this.loadTabData(tab);
  }
  
  loadTabData(tab) {
    switch (tab) {
      case "home":
        this.loadUserProfile();
        this.loadHomeData();
        break;
      case "emergency":
        this.loadEmergencyContacts();
        break;
      case "library":
        this.loadLibraryContent();
        break;
      case "download":
        this.loadDownloadsList();
        break;
      case "chat":
        this.loadForumStats();
        this.loadForumContent();
        break;
      case "profile":
        if (!this.profileData) this.loadUserProfile();
        break;
    }
  }
  
  // --- UTILITIES --- //
  openModal(modalId) {
    document.getElementById(modalId).style.display = "flex";
  }

  closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if(modal) {
      modal.style.display = "none";
      const form = modal.querySelector('form');
      if (form) form.reset();
    }
  }

  showNotification(message, type = "info") {
    const notification = document.createElement("div");
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
  }
  
  showConfirmation(message, onConfirm) {
      document.getElementById('confirmMessage').textContent = message;
      const confirmBtn = document.getElementById('confirmOK');
      
      // Clone and replace the button to remove old event listeners
      const newConfirmBtn = confirmBtn.cloneNode(true);
      confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

      newConfirmBtn.addEventListener('click', () => {
          this.closeModal('confirmModal');
          if(onConfirm) onConfirm();
      });
      
      this.openModal('confirmModal');
  }
}

document.addEventListener("DOMContentLoaded", () => {
  window.app = new MomCareApp();
});

// Add CSS for notifications and other dynamic elements
const style = document.createElement("style");
style.textContent = `
  .notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 20px;
    border-radius: 8px;
    color: white;
    font-weight: 500;
    z-index: 10000;
    animation: slideIn 0.3s ease;
  }
  .notification-success { background-color: #4CAF50; }
  .notification-error { background-color: #f44336; }
  .notification-info { background-color: #2196F3; }
  
  @keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
  }

  .post-item {
    background: #fff; padding: 15px; margin-bottom: 10px; border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05); cursor: pointer;
  }
  .post-item:hover { background: #f9f9f9; }
  .post-item h4 { margin-bottom: 5px; }
  .post-item p { font-size: 12px; color: #666; margin-bottom: 10px; }
  .like-btn {
    background: #eee; border: 1px solid #ddd; border-radius: 20px; padding: 5px 10px; cursor: pointer;
  }
  .reply-item { border-bottom: 1px solid #eee; padding: 10px 0; }
  .download-section { margin-bottom: 20px; }
`;
document.head.appendChild(style);


