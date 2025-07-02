<div class="sidebar">
    <h2 class="sidebar-title"><a href="/panda/crud" style="color: red;">HOME<a></h2>
    <div class="profile">
        <img src="<?php echo $this->Html->url('/img/profile-placeholder.png'); ?>" alt="Profile Picture" class="profile-pic">
        <p class="role">HR Admin</p>
        <p><?php echo $this->Html->link('View Profile', '#', ['class' => 'sidebar-link']); ?></p>
    </div>
    
    <p id="current-time" class="time">Loading time...</p>

    <ul class="sidebar-menu">
        <li><?php echo $this->Html->link('Lock Account', '#', ['class' => 'sidebar-link']); ?></li>
        <li><?php echo $this->Html->link('Sign Out', '#', ['class' => 'sidebar-link']); ?></li>
    </ul>
</div>

<script>
    function updateTime() {
        const now = new Date();
        const formattedTime = now.toLocaleString('en-US', {
            month: '2-digit', 
            day: '2-digit', 
            year: 'numeric', 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit',
            hour12: true
        });
        document.getElementById('current-time').textContent = formattedTime;
    }

    updateTime();
    setInterval(updateTime, 1000); // Update every second
</script>