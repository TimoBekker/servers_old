import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { Typography } from 'antd';

const { Title } = Typography;

const InformationSystemsList = () => {
  return (
    <div>
      <Title level={2}>Информационные системы</Title>
      <p>Здесь будет список информационных систем</p>
    </div>
  );
};

const InformationSystems = () => {
  return (
    <Routes>
      <Route path="/" element={<InformationSystemsList />} />
      <Route path="/:id" element={<div>Просмотр ИС</div>} />
      <Route path="/:id/edit" element={<div>Редактирование ИС</div>} />
      <Route path="/create" element={<div>Создание ИС</div>} />
    </Routes>
  );
};

export default InformationSystems;