# 在线聊天网站开发计划

## 1. 项目概述

用户要求开发一个基于 PHP + Vue 的在线聊天网站，具有以下特点：
- 简洁的界面设计
- 多端适配（响应式设计）
- 完整的管理系统

## 2. 技术栈

### 前端
- Vue 3 + Composition API
- TailwindCSS 3（响应式样式框架）
- Vue Router（路由管理）
- Axios（HTTP 请求）
- Lucide Icons（图标库）

### 后端
- PHP 8.x
- MySQL 数据库
- Slim Framework（轻量级 PHP 框架）
- JWT（身份认证）

## 3. 项目结构

```
/
├── backend/                    # PHP 后端
│   ├── src/                    # 源码目录
│   │   ├── controllers/        # 控制器
│   │   ├── models/             # 模型
│   │   ├── middleware/         # 中间件
│   │   ├── routes/             # 路由配置
│   │   └── utils/              # 工具类
│   ├── vendor/                 # Composer 依赖
│   ├── .htaccess               # Apache 配置
│   ├── composer.json           # Composer 配置
│   └── index.php               # 入口文件
├── frontend/                   # Vue 前端
│   ├── src/
│   │   ├── components/         # 组件
│   │   ├── views/              # 页面视图
│   │   ├── router/             # 路由配置
│   │   ├── utils/              # 工具函数
│   │   ├── App.vue             # 根组件
│   │   └── main.js             # 入口文件
│   ├── public/                 # 静态资源
│   ├── index.html              # HTML 模板
│   ├── package.json            # npm 配置
│   └── vite.config.js          # Vite 配置
├── database/                   # 数据库脚本
│   └── init.sql                # 初始化 SQL
└── README.md                   # 项目说明
```

## 4. 数据库设计

### 表结构

#### 4.1 users（用户表）
| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | INT | 主键，自增 |
| username | VARCHAR(50) | 用户名，唯一 |
| email | VARCHAR(100) | 邮箱，唯一 |
| password | VARCHAR(255) | 密码（哈希存储） |
| avatar | VARCHAR(255) | 头像路径 |
| role | ENUM('user', 'admin') | 用户角色 |
| status | ENUM('online', 'offline', 'busy') | 在线状态 |
| created_at | DATETIME | 创建时间 |
| updated_at | DATETIME | 更新时间 |

#### 4.2 messages（消息表）
| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | INT | 主键，自增 |
| from_id | INT | 发送者ID |
| to_id | INT | 接收者ID |
| content | TEXT | 消息内容 |
| type | ENUM('text', 'image', 'file') | 消息类型 |
| status | ENUM('sent', 'delivered', 'read') | 消息状态 |
| created_at | DATETIME | 创建时间 |

#### 4.3 conversations（对话表）
| 字段名 | 类型 | 说明 |
|--------|------|------|
| id | INT | 主键，自增 |
| user1_id | INT | 用户1 ID |
| user2_id | INT | 用户2 ID |
| last_message_id | INT | 最后消息ID |
| created_at | DATETIME | 创建时间 |
| updated_at | DATETIME | 更新时间 |

## 5. API 接口设计

### 5.1 用户认证

| 接口 | 方法 | 路径 | 说明 |
|------|------|------|------|
| 注册 | POST | /api/auth/register | 用户注册 |
| 登录 | POST | /api/auth/login | 用户登录 |
| 获取用户信息 | GET | /api/auth/user | 获取当前用户信息 |
| 登出 | POST | /api/auth/logout | 用户登出 |

### 5.2 消息管理

| 接口 | 方法 | 路径 | 说明 |
|------|------|------|------|
| 获取对话列表 | GET | /api/conversations | 获取用户对话列表 |
| 获取消息 | GET | /api/messages/:conversation_id | 获取对话消息 |
| 发送消息 | POST | /api/messages | 发送消息 |
| 标记已读 | PUT | /api/messages/:id/read | 标记消息已读 |

### 5.3 管理员接口

| 接口 | 方法 | 路径 | 说明 |
|------|------|------|------|
| 获取所有用户 | GET | /api/admin/users | 获取用户列表 |
| 删除用户 | DELETE | /api/admin/users/:id | 删除用户 |
| 获取消息日志 | GET | /api/admin/messages | 获取所有消息 |
| 删除消息 | DELETE | /api/admin/messages/:id | 删除消息 |

## 6. 前端页面设计

### 6.1 用户端页面

1. **登录页面** (`/login`)
   - 用户名/邮箱输入
   - 密码输入
   - 登录按钮
   - 注册链接

2. **注册页面** (`/register`)
   - 用户名输入
   - 邮箱输入
   - 密码输入
   - 确认密码
   - 注册按钮

3. **聊天主页面** (`/chat`)
   - 侧边栏：用户列表/对话列表
   - 主区域：消息列表、输入框

4. **个人设置** (`/settings`)
   - 头像上传
   - 密码修改
   - 状态设置

### 6.2 管理端页面

1. **管理员首页** (`/admin`)
   - 数据统计概览
   - 快捷操作

2. **用户管理** (`/admin/users`)
   - 用户列表
   - 用户搜索
   - 用户编辑/删除

3. **消息管理** (`/admin/messages`)
   - 消息日志
   - 消息搜索
   - 消息删除

## 7. 开发步骤

### 阶段一：项目初始化
1. 创建项目目录结构
2. 配置 Composer 和 npm
3. 安装依赖

### 阶段二：后端开发
1. 创建数据库连接
2. 实现模型层
3. 实现控制器
4. 配置路由
5. 实现 JWT 认证中间件

### 阶段三：前端开发
1. 创建页面组件
2. 配置路由
3. 实现聊天界面
4. 实现响应式布局

### 阶段四：管理系统开发
1. 创建管理页面组件
2. 实现管理员权限控制
3. 实现数据统计展示

### 阶段五：测试与部署
1. 功能测试
2. 响应式测试
3. 部署配置

## 8. 依赖清单

### 后端依赖
- slim/slim: ^4.0
- slim/psr7: ^1.0
- firebase/php-jwt: ^6.0
- illuminate/database: ^10.0
- vlucas/phpdotenv: ^5.0

### 前端依赖
- vue: ^3.0
- vue-router: ^4.0
- axios: ^1.0
- tailwindcss: ^3.0
- lucide-vue-next: ^0.26.0

## 9. 风险评估

| 风险 | 描述 | 应对措施 |
|------|------|----------|
| 跨域问题 | 前后端分离导致的跨域限制 | 配置 CORS 中间件 |
| 实时消息 | 消息无法实时推送 | 使用轮询或 WebSocket |
| 安全性 | 用户密码泄露风险 | 使用 bcrypt 哈希存储 |
| 性能 | 大量消息导致页面卡顿 | 消息分页加载 |

## 10. 响应式设计要点

- 使用 TailwindCSS 的响应式断点
- 移动端：单列布局，隐藏侧边栏
- 平板：双栏布局，缩小侧边栏
- 桌面端：完整双栏布局

## 11. 界面设计原则

- 简洁现代的设计风格
- 使用柔和的配色方案
- 清晰的信息层级
- 直观的用户交互