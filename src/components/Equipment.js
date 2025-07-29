import React, { useState, useEffect } from 'react';
import { Routes, Route } from 'react-router-dom';
import { Table, Button, Space, Input, Select, Card, Typography, message, Popconfirm } from 'antd';
import { PlusOutlined, EditOutlined, DeleteOutlined, EyeOutlined } from '@ant-design/icons';
import { equipmentService } from '../services/equipmentService';

const { Title } = Typography;
const { Search } = Input;
const { Option } = Select;

const EquipmentList = () => {
  const [equipment, setEquipment] = useState([]);
  const [loading, setLoading] = useState(false);
  const [pagination, setPagination] = useState({
    current: 1,
    pageSize: 30,
    total: 0,
  });
  const [filters, setFilters] = useState({});

  useEffect(() => {
    loadEquipment();
  }, [pagination.current, pagination.pageSize, filters]);

  const loadEquipment = async () => {
    try {
      setLoading(true);
      const params = {
        page: pagination.current,
        'per-page': pagination.pageSize,
        ...filters,
      };
      
      const response = await equipmentService.getEquipment(params);
      setEquipment(response.items || []);
      setPagination(prev => ({
        ...prev,
        total: response._meta?.totalCount || 0,
      }));
    } catch (error) {
      message.error('Ошибка загрузки оборудования');
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    try {
      await equipmentService.deleteEquipment(id);
      message.success('Оборудование удалено');
      loadEquipment();
    } catch (error) {
      message.error('Ошибка удаления оборудования');
    }
  };

  const handleTableChange = (newPagination, tableFilters, sorter) => {
    setPagination(newPagination);
  };

  const columns = [
    {
      title: '№',
      dataIndex: 'id',
      key: 'id',
      width: 60,
    },
    {
      title: 'Тип',
      dataIndex: ['type0', 'name'],
      key: 'type',
      width: 120,
    },
    {
      title: 'Hostname',
      dataIndex: 'name',
      key: 'name',
      render: (text, record) => (
        <Button type="link" onClick={() => window.open(`/equipment/${record.id}`, '_blank')}>
          {text}
        </Button>
      ),
    },
    {
      title: 'VMware-имя',
      dataIndex: 'vmware_name',
      key: 'vmware_name',
    },
    {
      title: 'Статус',
      dataIndex: ['state0', 'name'],
      key: 'state',
      width: 100,
    },
    {
      title: 'Организация',
      dataIndex: ['organization0', 'name'],
      key: 'organization',
      width: 150,
    },
    {
      title: 'Действия',
      key: 'actions',
      width: 120,
      render: (_, record) => (
        <Space size="small">
          <Button
            type="primary"
            size="small"
            icon={<EyeOutlined />}
            onClick={() => window.open(`/equipment/${record.id}`, '_blank')}
          />
          <Button
            size="small"
            icon={<EditOutlined />}
            onClick={() => window.open(`/equipment/${record.id}/edit`, '_blank')}
          />
          <Popconfirm
            title="Вы уверены, что хотите удалить это оборудование?"
            onConfirm={() => handleDelete(record.id)}
            okText="Да"
            cancelText="Нет"
          >
            <Button
              size="small"
              danger
              icon={<DeleteOutlined />}
            />
          </Popconfirm>
        </Space>
      ),
    },
  ];

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: 16 }}>
        <Title level={2}>Оборудование</Title>
        <Button type="primary" icon={<PlusOutlined />}>
          Добавить оборудование
        </Button>
      </div>

      <Card>
        <div style={{ marginBottom: 16, display: 'flex', gap: 16, flexWrap: 'wrap' }}>
          <Search
            placeholder="Поиск по названию"
            style={{ width: 200 }}
            onSearch={(value) => setFilters(prev => ({ ...prev, name: value }))}
          />
          <Select
            placeholder="Тип оборудования"
            style={{ width: 200 }}
            allowClear
            onChange={(value) => setFilters(prev => ({ ...prev, type: value }))}
          >
            <Option value="1">Виртуальный сервер</Option>
            <Option value="2">Физический сервер</Option>
          </Select>
          <Select
            placeholder="Статус"
            style={{ width: 150 }}
            allowClear
            onChange={(value) => setFilters(prev => ({ ...prev, state: value }))}
          >
            <Option value="1">В работе</Option>
            <Option value="2">Выключено</Option>
            <Option value="3">Выведено из эксплуатации</Option>
          </Select>
        </div>

        <Table
          columns={columns}
          dataSource={equipment}
          loading={loading}
          pagination={pagination}
          onChange={handleTableChange}
          rowKey="id"
          scroll={{ x: 1000 }}
        />
      </Card>
    </div>
  );
};

const Equipment = () => {
  return (
    <Routes>
      <Route path="/" element={<EquipmentList />} />
      <Route path="/:id" element={<div>Просмотр оборудования</div>} />
      <Route path="/:id/edit" element={<div>Редактирование оборудования</div>} />
      <Route path="/create" element={<div>Создание оборудования</div>} />
    </Routes>
  );
};

export default Equipment;