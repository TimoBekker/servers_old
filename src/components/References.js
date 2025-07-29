import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { Typography } from 'antd';

const { Title } = Typography;

const ReferencesList = () => {
  return (
    <div>
      <Title level={2}>Справочники</Title>
      <p>Здесь будут справочники</p>
    </div>
  );
};

const References = () => {
  return (
    <Routes>
      <Route path="/" element={<ReferencesList />} />
    </Routes>
  );
};

export default References;