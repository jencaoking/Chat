<template>
  <div class="h-screen flex flex-col bg-gray-100">
    <header class="bg-white shadow-sm px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
          {{ currentUser?.username?.charAt(0)?.toUpperCase() }}
        </div>
        <div>
          <h1 class="font-semibold text-gray-800">在线聊天</h1>
          <p class="text-sm text-gray-500">{{ currentUser?.username }}</p>
        </div>
      </div>
      <div class="flex items-center gap-4">
        <button
          v-if="currentUser?.role === 'admin'"
          @click="goToAdmin"
          class="flex items-center gap-2 px-3 py-2 bg-purple-500 text-white text-sm rounded-lg hover:bg-purple-600 transition-colors"
        >
          <Settings class="w-4 h-4" />
          <span class="hidden sm:inline">管理</span>
        </button>
        <button
          @click="handleLogout"
          class="flex items-center gap-2 px-3 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition-colors"
        >
          <LogOut class="w-4 h-4" />
          <span class="hidden sm:inline">退出</span>
        </button>
      </div>
    </header>
    
    <div class="flex-1 flex overflow-hidden">
      <aside class="w-full sm:w-80 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-4 border-b border-gray-200">
          <div class="relative">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              v-model="searchQuery"
              type="text"
              :placeholder="showAllUsers ? '搜索用户...' : '搜索联系人...'"
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
            />
          </div>
          <div class="mt-3 flex gap-2">
            <button
              @click="showAllUsers = false"
              :class="[
                'flex-1 py-1.5 px-3 rounded text-sm font-medium transition-colors',
                !showAllUsers ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              对话
            </button>
            <button
              @click="showAllUsers = true"
              :class="[
                'flex-1 py-1.5 px-3 rounded text-sm font-medium transition-colors',
                showAllUsers ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              用户
            </button>
          </div>
        </div>
        
        <div class="flex-1 overflow-y-auto scrollbar-hide">
          <template v-if="!showAllUsers">
            <div
              v-for="conv in filteredConversations"
              :key="conv.id"
              @click="selectConversation(conv)"
              :class="[
                'p-4 cursor-pointer border-b border-gray-100 hover:bg-gray-50 transition-colors',
                selectedConversation?.id === conv.id ? 'bg-blue-50 border-l-4 border-blue-500' : ''
              ]"
            >
              <div class="flex items-center gap-3">
                <div class="relative">
                  <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-semibold">
                    {{ conv.user?.username?.charAt(0)?.toUpperCase() }}
                  </div>
                  <div :class="['status-dot absolute -bottom-1 -right-1 border-2 border-white', `status-${conv.user?.status}`]"></div>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between">
                    <span class="font-medium text-gray-800 truncate">{{ conv.user?.username }}</span>
                    <span class="text-xs text-gray-400">{{ formatTime(conv.updated_at) }}</span>
                  </div>
                  <p class="text-sm text-gray-500 truncate mt-1">
                    {{ conv.last_message?.content || '暂无消息' }}
                  </p>
                </div>
              </div>
            </div>
            
            <div v-if="conversations.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
              <MessageCircle class="w-16 h-16 mb-4" />
              <p>暂无对话</p>
              <p class="text-sm">切换到「用户」标签开始聊天</p>
            </div>
          </template>
          
          <template v-else>
            <div
              v-for="user in filteredUsers"
              :key="user.id"
              @click="startConversation(user)"
              class="p-4 cursor-pointer border-b border-gray-100 hover:bg-gray-50 transition-colors"
            >
              <div class="flex items-center gap-3">
                <div class="relative">
                  <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-semibold">
                    {{ user.username?.charAt(0)?.toUpperCase() }}
                  </div>
                  <div :class="['status-dot absolute -bottom-1 -right-1 border-2 border-white', `status-${user.status}`]"></div>
                </div>
                <div class="flex-1 min-w-0">
                  <span class="font-medium text-gray-800">{{ user.username }}</span>
                  <p class="text-sm text-gray-500">{{ user.email }}</p>
                </div>
              </div>
            </div>
            
            <div v-if="users.length === 0" class="flex flex-col items-center justify-center py-12 text-gray-400">
              <Users class="w-16 h-16 mb-4" />
              <p>暂无其他用户</p>
            </div>
          </template>
        </div>
      </aside>
      
      <main class="flex-1 flex flex-col">
        <div v-if="selectedConversation" class="bg-white border-b border-gray-200 px-4 py-3 flex items-center gap-3">
          <div class="relative">
            <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-semibold">
              {{ selectedConversation.user?.username?.charAt(0)?.toUpperCase() }}
            </div>
            <div :class="['status-dot absolute -bottom-1 -right-1 border-2 border-white', `status-${selectedConversation.user?.status}`]"></div>
          </div>
          <div>
            <h2 class="font-medium text-gray-800">{{ selectedConversation.user?.username }}</h2>
            <p class="text-xs text-gray-500">{{ selectedConversation.user?.status === 'online' ? '在线' : '离线' }}</p>
          </div>
        </div>
        
        <div v-else class="flex-1 flex flex-col items-center justify-center text-gray-400">
          <MessageSquare class="w-20 h-20 mb-4" />
          <p class="text-lg">选择一个对话或用户开始聊天</p>
        </div>
        
        <div v-if="selectedConversation" ref="messagesContainer" class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50">
          <div
            v-for="msg in messages"
            :key="msg.id"
            :class="['flex', msg.is_me ? 'justify-end' : 'justify-start']"
          >
            <div :class="['message-bubble px-4 py-3', msg.is_me ? 'own' : 'other']">
              <p class="text-sm">{{ msg.content }}</p>
              <p :class="['text-xs mt-1', msg.is_me ? 'text-blue-200' : 'text-gray-400']">
                {{ formatTime(msg.created_at) }}
              </p>
            </div>
          </div>
        </div>
        
        <div v-if="selectedConversation" class="bg-white border-t border-gray-200 p-4">
          <form @submit.prevent="sendMessage" class="flex gap-3">
            <input
              v-model="newMessage"
              type="text"
              placeholder="输入消息..."
              class="flex-1 px-4 py-3 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none"
              @keyup.enter="sendMessage"
            />
            <button
              type="submit"
              :disabled="!newMessage.trim()"
              class="w-12 h-12 bg-blue-500 hover:bg-blue-600 text-white rounded-full flex items-center justify-center transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <Send class="w-5 h-5" />
            </button>
          </form>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Search, LogOut, Settings, MessageCircle, MessageSquare, Send, Users } from 'lucide-vue-next'
import { getUserFromStorage, logout } from '../utils/auth'
import axios from '../utils/axios'

const router = useRouter()
const currentUser = ref(getUserFromStorage())
const conversations = ref([])
const users = ref([])
const selectedConversation = ref(null)
const messages = ref([])
const newMessage = ref('')
const searchQuery = ref('')
const showAllUsers = ref(false)
const messagesContainer = ref(null)

const filteredConversations = computed(() => {
  if (!searchQuery.value) return conversations.value
  const query = searchQuery.value.toLowerCase()
  return conversations.value.filter(conv => 
    conv.user?.username?.toLowerCase().includes(query) ||
    conv.user?.email?.toLowerCase().includes(query)
  )
})

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value
  const query = searchQuery.value.toLowerCase()
  return users.value.filter(user => 
    user.username?.toLowerCase().includes(query) ||
    user.email?.toLowerCase().includes(query)
  )
})

const formatTime = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  const now = new Date()
  const diff = now - date
  
  if (diff < 60000) return '刚刚'
  if (diff < 3600000) return `${Math.floor(diff / 60000)}分钟前`
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}小时前`
  return date.toLocaleDateString('zh-CN')
}

const fetchConversations = async () => {
  try {
    const response = await axios.get('/conversations')
    if (response.success) {
      conversations.value = response.data
    }
  } catch (e) {
    console.error('Failed to fetch conversations:', e)
  }
}

const fetchUsers = async () => {
  try {
    const response = await axios.get('/users')
    if (response.success) {
      users.value = response.data
    }
  } catch (e) {
    console.error('Failed to fetch users:', e)
  }
}

const fetchMessages = async (conversationId) => {
  try {
    const response = await axios.get(`/messages/${conversationId}`)
    if (response.success) {
      messages.value = response.data
      await nextTick()
      scrollToBottom()
    }
  } catch (e) {
    console.error('Failed to fetch messages:', e)
  }
}

const selectConversation = (conv) => {
  selectedConversation.value = conv
  fetchMessages(conv.id)
}

const startConversation = async (user) => {
  const existingConv = conversations.value.find(
    conv => conv.user.id === user.id
  )
  
  if (existingConv) {
    selectConversation(existingConv)
    showAllUsers.value = false
    return
  }
  
  selectedConversation.value = {
    id: null,
    user: user
  }
  messages.value = []
  showAllUsers.value = false
}

const sendMessage = async () => {
  if (!newMessage.value.trim() || !selectedConversation.value) return
  
  try {
    const response = await axios.post('/messages', {
      to_id: selectedConversation.value.user.id,
      content: newMessage.value.trim(),
      type: 'text'
    })
    
    if (response.success) {
      if (!selectedConversation.value.id) {
        await fetchConversations()
        const newConv = conversations.value.find(
          conv => conv.user.id === selectedConversation.value.user.id
        )
        if (newConv) {
          selectedConversation.value = newConv
        }
      } else {
        await fetchConversations()
      }
      
      await fetchMessages(selectedConversation.value.id)
      newMessage.value = ''
    }
  } catch (e) {
    console.error('Failed to send message:', e)
  }
}

const scrollToBottom = () => {
  if (messagesContainer.value) {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
  }
}

const handleLogout = async () => {
  try {
    await logout()
  } catch (e) {
    console.error('Logout error:', e)
  }
  router.push('/login')
}

const goToAdmin = () => {
  router.push('/admin')
}

onMounted(() => {
  fetchConversations()
  fetchUsers()
  setInterval(() => {
    fetchConversations()
    fetchUsers()
  }, 5000)
})

watch(selectedConversation, (newVal) => {
  if (newVal && newVal.id) {
    fetchMessages(newVal.id)
  }
})
</script>