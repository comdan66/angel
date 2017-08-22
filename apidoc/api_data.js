define({ "api": [  {    "type": "get",    "url": "/send/audio",    "title": "傳語音",    "group": "Message",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "ori",            "description": "<p>語音網址，格式 m4a 檔案，需要 Https，網址長度最長 1000</p>"          },          {            "group": "Parameter",            "type": "Number",            "optional": false,            "field": "duration",            "description": "<p>語音長度，單位 milliseconds</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Message",    "name": "GetSendAudio"  },  {    "type": "get",    "url": "/send/image",    "title": "傳圖片",    "group": "Message",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "ori",            "description": "<p>原始圖片網址，需要 Https，網址長度最長 1000</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "prev",            "description": "<p>預覽圖片網址，需要 Https，網址長度最長 1000</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Message",    "name": "GetSendImage"  },  {    "type": "get",    "url": "/send/location",    "title": "傳定位",    "group": "Message",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "title",            "description": "<p>標題，最多 100 個字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "address",            "description": "<p>地址，最多 100 個字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "latitude",            "description": "<p>緯度</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "longitude",            "description": "<p>經度</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Message",    "name": "GetSendLocation"  },  {    "type": "get",    "url": "/send/message",    "title": "傳文字",    "group": "Message",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "text",            "description": "<p>文字訊息，最多 2000 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Message",    "name": "GetSendMessage"  },  {    "type": "get",    "url": "/send/sticker",    "title": "傳貼圖",    "group": "Message",    "header": {      "fields": {        "Header": [          {            "group": "Header",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          }        ]      }    },    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "package_id",            "description": "<p>package ID</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "sticker_id",            "description": "<p>sticker ID</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Message",    "name": "GetSendSticker"  },  {    "type": "get",    "url": "/send/video",    "title": "傳影片",    "group": "Message",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "ori",            "description": "<p>影片網址，格式 mp4 檔案，需要 Https，網址長度最長 1000</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "prev",            "description": "<p>預覽圖片網址，需要 Https，網址長度最長 1000</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Message",    "name": "GetSendVideo"  },  {    "type": "post",    "url": "/send/button",    "title": "傳按鈕",    "group": "Template",    "description": "<p>可以傳送多組按鈕</p>",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "alt",            "description": "<p>預覽訊息，最多 400 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "title",            "description": "<p>標題訊息，最多 40 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "text",            "description": "<p>文字訊息，沒有圖片最多 160 字元，有圖片最多 60 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "img",            "description": "<p>圖片網址，需要 Https，網址長度最長 1000</p>"          },          {            "group": "Parameter",            "type": "Array",            "optional": false,            "field": "actions",            "description": "<p>按鈕，最多 4 個</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.type",            "description": "<p>按鈕類型，有 uri、postback、message 三種</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.label",            "description": "<p>按鈕類型，按鈕文字，最多 20 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.uri",            "description": "<p>按鈕型態為 uri 時所需要的參數，反應的網址，可以接受 http、https、tel 三種協定</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.text",            "description": "<p>按鈕型態為 postback、message 時所需要的參數，按下者會重複回應此字串，型態為 postback 時為非必要選項</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.data",            "description": "<p>按鈕型態為 postback 時所需要的參數，用途不知道，還在研究</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Template",    "name": "PostSendButton"  },  {    "type": "post",    "url": "/send/carousel",    "title": "傳卡片",    "group": "Template",    "description": "<p>可以傳送卡片式的訊息，注意！所有的卡片的 Action 數量要相同</p>",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "alt",            "description": "<p>預覽訊息，最多 400 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "Array",            "optional": false,            "field": "columns",            "description": "<p>卡片，最多 5 個</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": true,            "field": "columns.title",            "description": "<p>卡片標題，最多 40 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "columns.text",            "description": "<p>卡片文字訊息，沒有圖片最多 160 字元，有圖片最多 60 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "columns.img",            "description": "<p>卡片圖片網址，需要 Https，網址長度最長 1000</p>"          },          {            "group": "Parameter",            "type": "Array",            "optional": false,            "field": "columns.actions",            "description": "<p>按鈕，最多三個，所有的卡片的 Action 數量要相同</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "columns.actions.type",            "description": "<p>按鈕類型，有 uri、postback、message 三種</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "columns.actions.label",            "description": "<p>按鈕類型，按鈕文字，最多 20 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "columns.actions.uri",            "description": "<p>按鈕型態為 uri 時所需要的參數，反應的網址，可以接受 http、https、tel 三種協定</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "columns.actions.text",            "description": "<p>按鈕型態為 postback、message 時所需要的參數，按下者會重複回應此字串，型態為 postback 時為非必要選項</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "columns.actions.data",            "description": "<p>按鈕型態為 postback 時所需要的參數，用途不知道，還在研究</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Template",    "name": "PostSendCarousel"  },  {    "type": "post",    "url": "/send/confirm",    "title": "傳確認",    "group": "Template",    "description": "<p>可以傳送選項式的訊息，注意！按鈕，一定要兩個</p>",    "parameter": {      "fields": {        "Parameter": [          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "user_id",            "description": "<p>接收者 User ID</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "alt",            "description": "<p>預覽訊息，最多 400 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "text",            "description": "<p>文字訊息，最多 240 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "Array",            "optional": false,            "field": "actions",            "description": "<p>按鈕，一定要兩個</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.type",            "description": "<p>按鈕類型，有 uri、postback、message 三種</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.label",            "description": "<p>按鈕類型，按鈕文字，最多 20 字元，中文一個字算 2 字元</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.uri",            "description": "<p>按鈕型態為 uri 時所需要的參數，反應的網址，可以接受 http、https、tel 三種協定</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.text",            "description": "<p>按鈕型態為 postback、message 時所需要的參數，按下者會重複回應此字串，型態為 postback 時為非必要選項</p>"          },          {            "group": "Parameter",            "type": "String",            "optional": false,            "field": "actions.data",            "description": "<p>按鈕型態為 postback 時所需要的參數，用途不知道，還在研究</p>"          }        ]      }    },    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "Boolean",            "optional": false,            "field": "status",            "description": "<p>執行狀態</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n{\n    \"status\": true\n}",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/send.php",    "groupTitle": "Template",    "name": "PostSendConfirm"  },  {    "type": "get",    "url": "/users",    "title": "取得使用者",    "group": "User",    "success": {      "fields": {        "Success 200": [          {            "group": "Success 200",            "type": "String",            "optional": false,            "field": "id",            "description": "<p>User ID</p>"          },          {            "group": "Success 200",            "type": "String",            "optional": false,            "field": "title",            "description": "<p>使用者名稱</p>"          }        ]      },      "examples": [        {          "title": "Success Response:",          "content": "HTTP/1.1 200 OK\n[\n    {\n        \"id\": \"U...\",\n        \"title\": \"吳政賢\"\n    }\n]",          "type": "json"        }      ]    },    "error": {      "fields": {        "Error 4xx": [          {            "group": "Error 4xx",            "type": "String",            "optional": false,            "field": "message",            "description": "<p>錯誤原因</p>"          }        ]      },      "examples": [        {          "title": "Error-Response:",          "content": "HTTP/1.1 405 Error\n{\n    \"message\": \"參數錯誤\"\n}",          "type": "json"        }      ]    },    "version": "0.0.0",    "filename": "root/application/controllers/api/users.php",    "groupTitle": "User",    "name": "GetUsers"  }] });
