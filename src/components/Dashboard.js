import React, { useState, useEffect } from 'react';
import { Card, Row, Col, Statistic, Timeline, Typography, Spin } from 'antd';
import {
  DesktopOutlined,
  DatabaseOutlined,
  CodeOutlined,
  FileTextOutlined,
  CalendarOutlined
} from '@ant-design/icons';
import api from '../services/api';

const { Title } = Typography;

const Dashboard = () => {
  const [stats, setStats] = useState({});
  const [events, setEvents] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadDashboardData();
  }, []);

  const loadDashboardData = async () => {
    try {
      setLoading(true);
      // Загружаем статистику и события
      const [statsResponse, eventsResponse] = await Promise.all([
        api.get('/dashboard/stats'),
        api.get('/dashboard/events')
      ]);
      
      setStats(statsResponse.data);
      setEvents(eventsResponse.data);
    } catch (error) {
      console.error('Ошибка загрузки данных дашборда:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div style={{ textAlign: 'center', padding: '50px' }}>
        <Spin size="large" />
      </div>
    );
  }

  return (
    <div>
      <Title level={2}>Главная панель</Title>
      
      <Row gutter={[16, 16]} style={{ marginBottom: 24 }}>
        <Col xs={24} sm={12} md={6}>
          <Card>
            <Statistic
              title="Оборудование"
              value={stats.equipment || 0}
              prefix={<DesktopOutlined />}
              valueStyle={{ color: '#3f8600' }}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} md={6}>
          <Card>
            <Statistic
              title="Информационные системы"
              value={stats.informationSystems || 0}
              prefix={<DatabaseOutlined />}
              valueStyle={{ color: '#1890ff' }}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} md={6}>
          <Card>
            <Statistic
              title="Дистрибутивы ПО"
              value={stats.software || 0}
              prefix={<CodeOutlined />}
              valueStyle={{ color: '#722ed1' }}
            />
          </Card>
        </Col>
        <Col xs={24} sm={12} md={6}>
          <Card>
            <Statistic
              title="Контракты"
              value={stats.contracts || 0}
              prefix={<FileTextOutlined />}
              valueStyle={{ color: '#eb2f96' }}
            />
          </Card>
        </Col>
      </Row>

      <Row gutter={[16, 16]}>
        <Col xs={24} lg={12}>
          <Card title="Последние события" extra={<CalendarOutlined />}>
            <Timeline>
              {events.map((event, index) => (
                <Timeline.Item key={index}>
                  <p><strong>{event.name}</strong></p>
                  <p>{event.description}</p>
                  <small>{new Date(event.date_begin).toLocaleString('ru-RU')}</small>
                </Timeline.Item>
              ))}
            </Timeline>
          </Card>
        </Col>
        <Col xs={24} lg={12}>
          <Card title="Быстрые действия">
            <Row gutter={[8, 8]}>
              <Col span={12}>
                <Card size="small" hoverable>
                  <div style={{ textAlign: 'center' }}>
                    <DesktopOutlined style={{ fontSize: '24px', marginBottom: '8px' }} />
                    <div>Добавить оборудование</div>
                  </div>
                </Card>
              </Col>
              <Col span={12}>
                <Card size="small" hoverable>
                  <div style={{ textAlign: 'center' }}>
                    <DatabaseOutlined style={{ fontSize: '24px', marginBottom: '8px' }} />
                    <div>Добавить ИС</div>
                  </div>
                </Card>
              </Col>
              <Col span={12}>
                <Card size="small" hoverable>
                  <div style={{ textAlign: 'center' }}>
                    <CodeOutlined style={{ fontSize: '24px', marginBottom: '8px' }} />
                    <div>Добавить ПО</div>
                  </div>
                </Card>
              </Col>
              <Col span={12}>
                <Card size="small" hoverable>
                  <div style={{ textAlign: 'center' }}>
                    <CalendarOutlined style={{ fontSize: '24px', marginBottom: '8px' }} />
                    <div>Создать событие</div>
                  </div>
                </Card>
              </Col>
            </Row>
          </Card>
        </Col>
      </Row>
    </div>
  );
};

export default Dashboard;