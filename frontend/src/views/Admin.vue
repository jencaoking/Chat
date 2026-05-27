<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow-sm px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center text-white font-bold">
          A
        </div>
        <div>
          <h1 class="font-semibold text-gray-800">管理后台</h1>
          <p class="text-sm text-gray-500">管理员面板</p>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <button
          @click="goToChat"
          class="flex items-center gap-2 px-3 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600 transition-colors"
        >
          <MessageCircle class="w-4 h-4" />
          <span>聊天</span>
        </button>
        <button
          @click="handleLogout"
          class="flex items-center gap-2 px-3 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors"
        >
          <LogOut class="w-4 h-4" />
          <span>退出</span>
        </button>
      </div>
    </header>
    
    <div class="p-4 sm:p-6">
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">总用户数</p>
              <p class="text-3xl font-bold text-blue-500">{{ stats.total_users || 0 }}</p>
            </div>
            <Users class="w-12 h-12 text-blue-400" />
          </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">总消息数</p>
              <p class="text-3xl font-bold text-green-500">{{ stats.total_messages || 0 }}</p>
            </div>
            <MessageSquare class="w-12 h-12 text-green-400" />
          </div>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-500">在线用户</p>
              <p class="text-3xl font-bold text-purple-500">{{ stats.online_users || 0 }}</p>
            </div>
            <Circle class="w-12 h-12 text-purple-400" />
          </div>
        </div>
      </div>
      
      <div class="flex gap-4 mb-6 overflow-x-auto pb-2">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="activeTab = tab.id"
          :class="[
            'flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap',
            activeTab === tab.id ? 'bg-purple-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100'
          ]"
        >
          <component :is="tab.icon" class="w-5 h-5" />
          <span>{{ tab.name }}</span>
        </button>
      </div>
      
      <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
          <h2 class="font-semibold text-gray-800">{{ tabs.find(t => t.id === activeTab)?.name }}</h2>
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="搜索..."
              class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none"
            />
          </div>
        </div>
        
        <div v-if="activeTab === 'users'" class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">ID</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">用户名</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">邮箱</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">角色</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">状态</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">注册时间</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">操作</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="user in filteredUsers" :key="user.id" class="border-b border-gray-100 hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-600">{{ user.id }}</td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-semibold text-sm">
                      {{ user.username?.charAt(0)?.toUpperCase() }}
                    </div>
                    <span class="text-sm text-gray-800">{{ user.username }}</span>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ user.email }}</td>
                <td class="px-4 py-3">
                  <span :class="[
                    'px-2 py-1 text-xs rounded-full',
                    user.role === 'admin' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'
                  ]">
                    {{ user.role === 'admin' ? '管理员' : '普通用户' }}
                  </span>
                </td>
                <td class="px-4 py-3">
                  <div class="flex items-center gap-2">
                    <div :class="['status-dot', `status-${user.status}`]"></div>
                    <span class="text-sm text-gray-600">
                      {{ user.status === 'online' ? '在线' : user.status === 'busy' ? '忙碌' : '离线' }}
                    </span>
                  </div>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ formatDate(user.created_at) }}</td>
                <td class="px-4 py-3">
                  <button
                    @click="deleteUser(user.id)"
                    class="flex items-center gap-1 px-2 py-1 text-red-500 hover:bg-red-50 rounded transition-colors"
                  >
                    <Trash2 class="w-4 h-4" />
                    <span class="text-sm">删除</span>
                  </button>
                </td>
              </tr>
              <tr v-if="users.length === 0">
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                  <Users class="w-12 h-12 mx-auto mb-2" />
                  <p>暂无用户</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <div v-if="activeTab === 'messages'" class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="bg-gray-50">
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">ID</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">发送者</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">接收者</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">内容</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">状态</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">发送时间</th>
                <th class="px-4 py-3 text-left text-sm font-medium text-gray-600">操作</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="msg in filteredMessages" :key="msg.id" class="border-b border-gray-100 hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-600">{{ msg.id }}</td>
                <td class="px-4 py-3 text-sm text-gray-800">{{ msg.from?.username }}</td>
                <td class="px-4 py-3 text-sm text-gray-800">{{ msg.to?.username }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 max-w-xs truncate">{{ msg.content }}</td>
                <td class="px-4 py-3">
                  <span :class="[
                    'px-2 py-1 text-xs rounded-full',
                    msg.status === 'read' ? 'bg-green-100 text-green-600' : msg.status === 'delivered' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600'
                  ]">
                    {{ msg.status === 'read' ? '已读' : msg.status === 'delivered' ? '已送达' : '已发送' }}
                  </span>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ formatDate(msg.created_at) }}</td>
                <td class="px-4 py-3">
                  <button
                    @click="deleteMessage(msg.id)"
                    class="flex items-center gap-1 px-2 py-1 text-red-500 hover:bg-red-50 rounded transition-colors"
                  >
                    <Trash2 class="w-4 h-4" />
                    <span class="text-sm">删除</span>
                  </button>
                </td>
              </tr>
              <tr v-if="messages.length === 0">
                <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                  <MessageSquare class="w-12 h-12 mx-auto mb-2" />
                  <p>暂无消息</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
    <div v-if="showConfirm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 max-w-md mx-4">
        <div class="flex items-center gap-3 mb-4">
          <AlertCircle class="w-10 h-10 text-red-500" />
          <div>
            <h3 class="font-semibold text-gray-800">确认删除</h3>
            <p class="text-sm text-gray-500">{{ confirmMessage }}</p>
          </div>
        </div>
        <div class="flex gap-3 justify-end">
          <button
            @click="showConfirm = false"
            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition-colors"
          >
            取消
          </button>
          <button
            @click="confirmDelete"
            class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors"
          >
            确认删除
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, markRaw } from 'vue'
import { Users, MessageSquare, Circle, Search, LogOut, MessageCircle, Trash2, AlertCircle } from 'lucide-vue-next'
import { logout } from '../utils/auth'
import axios from '../utils/axios'

const tabs = [
  { id: 'users', name: '用户管理', icon: markRaw(Users) },
  { id: 'messages', name: '消息管理', icon: markRaw(MessageSquare) }
]

const activeTab = ref('users')
const users = ref([])
const messages = ref([])
const stats = ref({})
const searchQuery = ref('')
const showConfirm = ref(false)
const confirmMessage = ref('')
const deleteId = ref(null)
const deleteType = ref('')

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user =>
    user.username.toLowerCase().includes(query) ||
    user.email.toLowerCase().includes(query)
  )
})

const filteredMessages = computed(() => {
  if (!searchQuery.value) return messages.value
  const query = searchQuery.value.toLowerCase()
  return messages.value.filter(msg =>
    msg.from?.username.toLowerCase().includes(query) ||
    msg.to?.username.toLowerCase().includes(query) ||
    msg.content.toLowerCase().includes(query)
  )
})

const formatDate = (dateString) => {
  if (!dateString) return ''
  return new Date(dateString).toLocaleString('zh-CN')
}

const fetchUsers = async () => {
  try {
    const response = await axios.get('/admin/users')
    if (response.success) {
      users.value = response.data
    }
  } catch (e) {
    console.error('Failed to fetch users:', e)
  }
}

const fetchMessages = async () => {
  try {
    const response = await axios.get('/admin/messages')
    if (response.success) {
      messages.value = response.data
    }
  } catch (e) {
    console.error('Failed to fetch messages:', e)
  }
}

const fetchStats = async () => {
  try {
    const response = await axios.get('/admin/stats')
    if (response.success) {
      stats.value = response.data
    }
  } catch (e) {
    console.error('Failed to fetch stats:', e)
  }
}

const deleteUser = (id) => {
  deleteId.value = id
  deleteType.value = 'user'
  confirmMessage.value = '确定要删除该用户吗？此操作不可撤销。'
  showConfirm.value = true
}

const deleteMessage = (id) => {
  deleteId.value = id
  deleteType.value = 'message'
  confirmMessage.value = '确定要删除这条消息吗？此操作不可撤销。'
  showConfirm.value = true
}

const confirmDelete = async () => {
  try {
    if (deleteType.value === 'user') {
      await axios.delete(`/admin/users/${deleteId.value}`)
      users.value = users.value.filter(u => u.id !== deleteId.value)
    } else {
      await axios.delete(`/admin/messages/${deleteId.value}`)
      messages.value = messages.value.filter(m => m.id !== deleteId.value)
    }
  } catch (e) {
    console.error('Delete failed:', e)
  } finally {
    showConfirm.value = false
  }
}

const handleLogout = async () => {
  await logout()
  window.location.href = '/login'
}

const goToChat = () => {
  window.location.href = '/chat'
}

onMounted(() => {
  fetchUsers()
  fetchMessages()
  fetchStats()
})
</script>