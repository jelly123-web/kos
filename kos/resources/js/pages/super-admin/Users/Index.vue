<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import DefaultLayoutWithVerticalNav from '@/layouts/components/DefaultLayoutWithVerticalNav.vue'
import DemoSimpleTableBasics from '@/views/pages/tables/DemoSimpleTableBasics.vue'

const props = defineProps({
  users: Array
})

const form = useForm({
  name: '',
  email: '',
  password: '',
  role: 'tenant',
})

const deleteUser = (id) => {
  if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
    form.delete(route('super-admin.users.delete', id))
  }
}

const submit = () => {
  form.post(route('super-admin.users.create'), {
    onSuccess: () => form.reset(),
  })
}
</script>

<template>
  <DefaultLayoutWithVerticalNav>
    <Head title="Manajemen User" />

    <VRow>
      <VCol cols="12">
        <VCard title="Daftar Pengguna Sistem">
          <VCardText>
            <VTable>
              <thead>
                <tr>
                  <th>Nama</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="user in users" :key="user.id">
                  <td>{{ user.name }}</td>
                  <td>{{ user.email }}</td>
                  <td>
                    <VChip
                      :color="user.role === 'super_admin' ? 'error' : 'primary'"
                      size="small"
                    >
                      {{ user.role }}
                    </VChip>
                  </td>
                  <td>
                    <VChip
                      :color="user.status === 'active' ? 'success' : 'secondary'"
                      size="small"
                    >
                      {{ user.status }}
                    </VChip>
                  </td>
                  <td>
                    <VBtn
                      v-if="user.role !== 'super_admin'"
                      icon
                      variant="text"
                      color="error"
                      @click="deleteUser(user.id)"
                    >
                      <VIcon icon="mdi-delete-outline" />
                    </VBtn>
                  </td>
                </tr>
              </tbody>
            </VTable>
          </VCardText>
        </VCard>
      </VCol>

      <VCol cols="12">
        <VCard title="Tambah User Baru">
          <VCardText>
            <VForm @submit.prevent="submit">
              <VRow>
                <VCol cols="12" md="6">
                  <VTextField
                    v-model="form.name"
                    label="Nama Lengkap"
                    placeholder="John Doe"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <VTextField
                    v-model="form.email"
                    label="Email"
                    placeholder="johndoe@example.com"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <VTextField
                    v-model="form.password"
                    label="Password"
                    type="password"
                  />
                </VCol>
                <VCol cols="12" md="6">
                  <VSelect
                    v-model="form.role"
                    label="Role"
                    :items="['admin', 'owner', 'staff', 'tenant']"
                  />
                </VCol>
                <VCol cols="12">
                  <VBtn type="submit" color="primary">
                    Simpan User
                  </VBtn>
                </VCol>
              </VRow>
            </VForm>
          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </DefaultLayoutWithVerticalNav>
</template>
