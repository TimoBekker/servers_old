import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { Typography } from 'antd';

const { Title } = Typography;

const ReportsList = () => {
  return (
    <div>
      <Title level={2}>Отчеты</Title>
      <p>Здесь будут отчеты</p>
    </div>
  );
};

const Reports = () => {
  return (
    <Routes>
      <Route path="/" element={<ReportsList />} />
    </Routes>
  );
};

export default Reports;