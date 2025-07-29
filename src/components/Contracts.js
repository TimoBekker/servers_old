import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { Typography } from 'antd';

const { Title } = Typography;

const ContractsList = () => {
  return (
    <div>
      <Title level={2}>Контракты</Title>
      <p>Здесь будет список контрактов</p>
    </div>
  );
};

const Contracts = () => {
  return (
    <Routes>
      <Route path="/" element={<ContractsList />} />
      <Route path="/:id" element={<div>Просмотр контракта</div>} />
      <Route path="/:id/edit" element={<div>Редактирование контракта</div>} />
      <Route path="/create" element={<div>Создание контракта</div>} />
    </Routes>
  );
};

export default Contracts;