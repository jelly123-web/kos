<script setup>
import { Head } from '@inertiajs/vue3'
import DefaultLayoutWithVerticalNav from '@/layouts/components/DefaultLayoutWithVerticalNav.vue'
import AnalyticsTransactions from '@/views/dashboard/AnalyticsTransactions.vue'
import AnalyticsWeeklyOverview from '@/views/dashboard/AnalyticsWeeklyOverview.vue'
import AnalyticsTotalEarning from '@/views/dashboard/AnalyticsTotalEarning.vue'

const props = defineProps({
  stats: Object
})

const statistics = [
  {
    title: 'Total Users',
    stats: props.stats.total_users,
    icon: 'mdi-account-outline',
    color: 'primary',
  },
  {
    title: 'Admins',
    stats: props.stats.admins,
    icon: 'mdi-shield-account-outline',
    color: 'success',
  },
  {
    title: 'Owners',
    stats: props.stats.owners,
    icon: 'mdi-home-account',
    color: 'warning',
  },
  {
    title: 'Staff',
    stats: props.stats.staff,
    icon: 'mdi-worker',
    color: 'info',
  },
]
</script>

<template>
  <DefaultLayoutWithVerticalNav>
    <Head title="Super Admin Dashboard" />

    <VRow class="match-height">
      <VCol cols="12">
        <VCard title="Sistem Statistik">
          <VCardText>
            <VRow>
              <VCol
                v-for="item in statistics"
                :key="item.title"
                cols="12"
                sm="3"
              >
                <div class="d-flex align-center">
                  <VAvatar
                    :color="item.color"
                    variant="tonal"
                    size="42"
                    class="me-3"
                  >
                    <VIcon
                      :icon="item.icon"
                      size="24"
                    />
                  </VAvatar>
                  <div>
                    <p class="text-body-1 mb-0">
                      {{ item.title }}
                    </p>
                    <h5 class="text-h5">
                      {{ item.stats }}
                    </h5>
                  </div>
                </div>
              </VCol>
            </VRow>
          </VCardText>
        </VCard>
      </VCol>

      <VCol
        cols="12"
        md="8"
      >
        <VCard title="Aktivitas Terbaru">
          <VCardText>
            <!-- Placeholder for recent activities -->
            <p>Fitur monitoring aktivitas user akan tampil di sini.</p>
          </VCardText>
        </VCard>
      </VCol>

      <VCol
        cols="12"
        md="4"
      >
        <VCard title="Penyimpanan Sistem">
          <VCardText>
            <div class="d-flex align-center mb-3">
              <VProgressCircular
                :model-value="45"
                color="primary"
                size="64"
                width="7"
              >
                45%
              </VProgressCircular>
              <div class="ms-4">
                <p class="text-body-1 mb-0">Database Usage</p>
                <h6 class="text-h6">1.2 GB / 5 GB</h6>
              </div>
            </div>
            <VBtn
              block
              color="primary"
              variant="outlined"
            >
              Backup Sekarang
            </VBtn>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </DefaultLayoutWithVerticalNav>
</template>
