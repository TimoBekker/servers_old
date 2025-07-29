import React from 'react';
import { Routes, Route } from 'react-router-dom';
import { Typography } from 'antd';

const { Title } = Typography;

const EventsList = () => {
  return (
    <div>
      <Title level={2}>События</Title>
      <p>Здесь будет список событий</p>
    </div>
  );
};

const Events = () => {
  return (
    <Routes>
      <Route path="/" element={<EventsList />} />
      <Route path="/:id" element={<div>Просмотр события</div>} />
      }
      <Route path="/:id/edit" element={<div>Редактирование события</div>} />
      }
      <Route path="/create" element={<div>Создание события</div>} />
      }
    </Routes>
  );
};

export default Events;