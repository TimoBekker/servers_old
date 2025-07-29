import React, { useState, useEffect } from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { Layout, Menu, Spin, message } from 'antd';
import {
  DesktopOutlined,
  DatabaseOutlined,
  CodeOutlined,
  FileTextOutlined,
  CalendarOutlined,
  SettingOutlined,
  BarChartOutlined,
  UserOutlined,
  LogoutOutlined
} from '@ant-design/icons';
import Login from './components/Login';
import Dashboard from './components/Dashboard';
import Equipment from './components/Equipment';
import InformationSystems from './components/InformationSystems';
import Software from './components/Software';
import Contracts from './components/Contracts';
import Events from './components/Events';
import References from './components/References';
import Reports from './components/Reports';
import { authService } from './services/authService';
import './App.css';

const { Header, Sider, Content } = Layout;

function App() {
  const [collapsed, setCollapsed] = useState(false);
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    checkAuth();
  }, []);

  const checkAuth = async () => {
    try {
      const userData = await authService.getCurrentUser();
      setUser(userData);
    } catch (error) {
      console.log('Пользователь не авторизован');
    } finally {
      setLoading(false);
    }
  };

  const handleLogin = async (credentials) => {
    try {
      const userData = await authService.login(credentials);
      setUser(userData);
      message.success('Успешный вход в систему');
    } catch (error) {
      message.error('Ошибка авторизации');
      throw error;
    }
  };

  const handleLogout = async () => {
    try {
      await authService.logout();
      setUser(null);
      message.success('Вы вышли из системы');
    } catch (error) {
      message.error('Ошибка при выходе');
    }
  };

  if (loading) {
    return (
      <div style={{ 
        display: 'flex', 
        justifyContent: 'center', 
        alignItems: 'center', 
        height: '100vh' 
      }}>
        <Spin size="large" />
      </div>
    );
  }

  if (!user) {
    return <Login onLogin={handleLogin} />;
  }

  const menuItems = [
    {
      key: '/',
      icon: <DesktopOutlined />,
      label: 'Главная',
    },
    {
      key: '/equipment',
      icon: <DatabaseOutlined />,
      label: 'Оборудование',
    },
    {
      key: '/information-systems',
      icon: <CodeOutlined />,
      label: 'ИС',
    },
    {
      key: '/software',
      icon: <CodeOutlined />,
      label: 'ПО',
      children: [
        {
          key: '/software/distributions',
          label: 'Дистрибутивы',
        },
        {
          key: '/software/installed',
          label: 'Установленное ПО',
        },
      ],
    },
    {
      key: '/contracts',
      icon: <FileTextOutlined />,
      label: 'Контракты',
    },
    {
      key: '/events',
      icon: <CalendarOutlined />,
      label: 'События',
    },
    {
      key: '/references',
      icon: <SettingOutlined />,
      label: 'Справочники',
    },
    {
      key: '/reports',
      icon: <BarChartOutlined />,
      label: 'Отчеты',
    },
  ];

  return (
    <Layout style={{ minHeight: '100vh' }}>
      <Sider collapsible collapsed={collapsed} onCollapse={setCollapsed}>
        <div className="logo">
          {collapsed ? 'РС' : 'Система учета'}
        </div>
        <Menu
          theme="dark"
          defaultSelectedKeys={['/']}
          mode="inline"
          items={menuItems}
        />
      </Sider>
      <Layout className="site-layout">
        <Header className="site-layout-background" style={{ padding: 0, background: '#fff' }}>
          <div style={{ 
            display: 'flex', 
            justifyContent: 'space-between', 
            alignItems: 'center',
            padding: '0 24px'
          }}>
            <h2 style={{ margin: 0 }}>Система учета серверов и информационных систем</h2>
            <div style={{ display: 'flex', alignItems: 'center', gap: '16px' }}>
              <span>
                <UserOutlined /> {user.last_name} {user.first_name}
              </span>
              <LogoutOutlined 
                onClick={handleLogout}
                style={{ cursor: 'pointer', fontSize: '16px' }}
                title="Выйти"
              />
            </div>
          </div>
        </Header>
        <Content style={{ margin: '0 16px' }}>
          <div className="site-layout-content">
            <Routes>
              <Route path="/" element={<Dashboard />} />
              <Route path="/equipment/*" element={<Equipment />} />
              <Route path="/information-systems/*" element={<InformationSystems />} />
              <Route path="/software/*" element={<Software />} />
              <Route path="/contracts/*" element={<Contracts />} />
              <Route path="/events/*" element={<Events />} />
              <Route path="/references/*" element={<References />} />
              <Route path="/reports/*" element={<Reports />} />
              <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
          </div>
        </Content>
      </Layout>
    </Layout>
  );
}

export default App;