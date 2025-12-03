<script setup>
// The router-view component handles rendering the correct page based on the URL
</script>

<template>
  <div class="app-layout">
    <nav class="navbar">
      <div class="brand">MediBook</div>
      <div class="nav-links">
        <!-- You can add common navigation links here if needed -->
      </div>
    </nav>

    <main class="main-content">
      <router-view></router-view>
    </main>
  </div>
</template>

<style>
/* Global Resets */
body { margin: 0; background-color: #f4f6f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

.navbar {
  background: white;
  padding: 1rem 2rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.brand { font-weight: bold; font-size: 1.5rem; color: #007bff; }

.main-content {
  /* Ensure content doesn't hit the edges */
  padding: 0;
  min-height: calc(100vh - 60px);
}
</style>