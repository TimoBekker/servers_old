<?php
// ps при загрузке стоит проверка только по майм типам теперь!
return [
    'adminEmail' => 'admin@example.com',
    'aviableFileUploadExtensions' => 'doc, docx, xls, xlsx, txt, rtf, pdf, odt, jpg, jpeg, png, gif, zip, rar, ppt, pptx, vsd, vsdx',
    'aviableFileUploadMimetypes' => '
        application/vnd.visio,
        application/msword,
        application/vnd.openxmlformats-officedocument.wordprocessingml.document,
        application/vnd.ms-powerpoint,
        application/x-rar-compressed,
        application/octet-stream,
        application/zip,
        image/gif,
        image/png,
        image/jpeg,
        image/pjpeg,
        image/jpeg,
        image/pjpeg,
        application/pdf,
        text/plain,
        application/rtf,
        application/x-rtf,
        text/richtext,
        application/excel,
        application/vnd.ms-excel,
        application/x-excel,
        application/x-msexcel,
        application/vnd.ms-office
    ',
    'TYPE_HINT_ERROR' => 'Передан неверный тип параметра',
];
