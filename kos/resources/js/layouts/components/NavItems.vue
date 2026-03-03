<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue'
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const userRole = computed(() => page.props.auth?.user?.role)
</script>

<template>
  <!-- 👉 Super Admin Menu -->
  <template v-if="userRole === 'super_admin'">
    <VerticalNavSectionTitle :item="{ title: 'SUPER ADMIN' }" />
    <VerticalNavLink
      :item="{
        title: 'Dashboard',
        icon: 'mdi-home-outline',
        to: route('super-admin.dashboard'),
      }"
    />
    <VerticalNavLink
      :item="{
        title: 'Manajemen User',
        icon: 'mdi-account-group-outline',
        to: route('super-admin.users'),
      }"
    />
  </template>

  <!-- 👉 Admin Menu (TBA) -->
  <template v-if="userRole === 'admin'">
    <VerticalNavSectionTitle :item="{ title: 'ADMIN' }" />
    <VerticalNavLink
      :item="{
        title: 'Dashboard',
        icon: 'mdi-home-outline',
        to: '/admin/dashboard',
      }"
    />
  </template>

  <!-- 👉 Logout -->
  <VerticalNavSectionTitle :item="{ title: 'SISTEM' }" />
  <VerticalNavLink
    :item="{
      title: 'Logout',
      icon: 'mdi-logout',
      href: '/logout',
      method: 'post',
    }"
  />
</template>
