-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: negocio_web
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bancos`
--

DROP TABLE IF EXISTS `bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bancos` (
  `id_banco` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `numero_cuenta` varchar(100) NOT NULL,
  `id_tipo_cuenta` int(11) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_banco`),
  KEY `id_tipo_cuenta` (`id_tipo_cuenta`),
  CONSTRAINT `bancos_ibfk_1` FOREIGN KEY (`id_tipo_cuenta`) REFERENCES `tipos_cuenta_banco` (`id_tipo_cuenta`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bancos`
--

/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (1,'Banco Atlántida','12345678',1,'1772921723_Captura de pantalla 2025-07-20 163548.png');
/*!40000 ALTER TABLE `bancos` ENABLE KEYS */;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `banners` (
  `id_banner` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `texto_boton` varchar(100) DEFAULT 'Ver más',
  `enlace` varchar(255) DEFAULT '#',
  `orden` int(11) DEFAULT 0,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_banner`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (1,'Productos nuevos','Hasta el 30% en descuento','banner_1772490123_69a60d8b896e8.jpg','ver ahora','#productos',1,'activo','2026-02-24 22:59:07'),(2,'Las mejores ofertas ','Hasta el 30% en descuento','banner_1772494218_69a61d8aac415.jpg','Ver Ofertas','#ofertas',2,'activo','2026-02-24 23:09:32');
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;

--
-- Table structure for table `carrito_detalle`
--

DROP TABLE IF EXISTS `carrito_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carrito_detalle` (
  `id_carrito_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `id_carrito` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_carrito_detalle`),
  KEY `id_carrito` (`id_carrito`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `carrito_detalle_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carritos` (`id_carrito`),
  CONSTRAINT `carrito_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito_detalle`
--

/*!40000 ALTER TABLE `carrito_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `carrito_detalle` ENABLE KEYS */;

--
-- Table structure for table `carritos`
--

DROP TABLE IF EXISTS `carritos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carritos` (
  `id_carrito` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `estado` enum('activo','comprado','cancelado') DEFAULT 'activo',
  PRIMARY KEY (`id_carrito`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `carritos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carritos`
--

/*!40000 ALTER TABLE `carritos` DISABLE KEYS */;
INSERT INTO `carritos` VALUES (17,22,'2026-03-03 00:23:02','comprado'),(18,22,'2026-03-04 14:31:09','comprado'),(19,22,'2026-03-05 13:30:53','comprado'),(20,22,'2026-03-05 14:55:32','comprado'),(21,22,'2026-03-05 14:56:54','comprado'),(22,22,'2026-03-05 15:01:27','comprado'),(23,22,'2026-03-05 15:13:57','comprado'),(24,22,'2026-03-05 15:14:22','comprado'),(25,22,'2026-03-05 15:25:56','comprado'),(26,22,'2026-03-05 15:27:53','comprado'),(27,22,'2026-03-05 15:28:51','comprado'),(28,22,'2026-03-05 15:37:09','comprado');
/*!40000 ALTER TABLE `carritos` ENABLE KEYS */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `id_padre` int(11) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `icono` varchar(100) DEFAULT NULL,
  `tasa_impuesto` decimal(5,2) DEFAULT NULL COMMENT 'Tasa de impuesto (%). NULL = hereda del padre o usa 0',
  PRIMARY KEY (`id_categoria`),
  KEY `idx_id_padre` (`id_padre`),
  CONSTRAINT `fk_categoria_padre` FOREIGN KEY (`id_padre`) REFERENCES `categorias` (`id_categoria`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (4,NULL,'Smartphones','electrica','activo','fa-mobile-screen',15.00),(9,4,'Gama alta','','activo','',NULL),(10,NULL,'Computadoras','','activo','fa-laptop',7.00),(11,10,'Laptops','','activo','',NULL),(14,NULL,'Componentes de PC','','activo','fa-laptop',15.00),(15,14,'Tarjetas gráficas','','activo','',NULL),(16,4,'Gama media','','activo','',NULL),(17,4,'Gama económica','','activo','',NULL),(18,4,'Reacondicionados','','activo','',NULL),(19,10,'PCs de escritorio','','activo','',NULL),(20,10,'Gaming','','activo','',NULL),(21,14,'Placas base','','activo','',NULL),(22,14,'Memoria RAM','','activo','',NULL),(23,NULL,'Televisores y Audio','','activo','fa-headphones',15.00),(24,23,'Smart TV','','activo','',NULL),(25,23,'Barras de sonido','','activo','',NULL),(26,23,'Audifonos','','activo','',NULL),(27,NULL,'Videojuegos','','activo','fa-gamepad',15.00),(28,27,'Consolas','','activo','',NULL),(29,27,'Mandos','','activo','',NULL),(30,27,'Sillas gamer','','activo','',NULL),(31,27,'Teclado Gamer','','activo','',NULL),(32,27,'Mouse Gamer','','activo','',NULL);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('activo','bloqueado') DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cliente`),
  KEY `fk_clientes_usuarios` (`id_usuario`),
  CONSTRAINT `fk_clientes_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (22,33,'edurado1201','activo','2026-03-03 06:23:00');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;

--
-- Table structure for table `configuracion`
--

DROP TABLE IF EXISTS `configuracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_negocio` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `texto_inicio` text DEFAULT NULL,
  `redes_sociales` text DEFAULT NULL,
  `moneda` varchar(10) DEFAULT 'USD',
  `slogan` varchar(255) DEFAULT NULL,
  `horario_atencion` varchar(255) DEFAULT NULL,
  `pie_pagina` text DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `texto_banner_superior` varchar(255) DEFAULT NULL,
  `header_menu` text DEFAULT NULL,
  `footer_columns` text DEFAULT NULL,
  `hero_etiqueta` varchar(100) DEFAULT NULL,
  `hero_titulo` varchar(200) DEFAULT NULL,
  `hero_subtitulo` varchar(200) DEFAULT NULL,
  `hero_descripcion` text DEFAULT NULL,
  `hero_imagen` varchar(255) DEFAULT NULL,
  `hero_btn_primario` varchar(100) DEFAULT 'Comprar Ahora',
  `hero_btn_secundario` varchar(100) DEFAULT 'Ver Catálogo',
  `color_primary` varchar(7) DEFAULT NULL,
  `color_primary_dark` varchar(7) DEFAULT NULL,
  `color_background_light` varchar(7) DEFAULT NULL,
  `color_background_dark` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`id_config`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion`
--

/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` VALUES (1,'TechNova','logo_1772490800.png','9999-9999','contacto@minegocio.com','Dirección del negocio','Bienvenido a nuestro negocio','{\"facebook\":\"https://www.facebook.com/?locale=es_LA\",\"instagram\":\"https://www.instagram.com/\",\"whatsapp\":\"\",\"tiktok\":\"\",\"twitter\":\"https://x.com/?lang=es\",\"youtube\":\"\"}','HNL','Tu mejor tienda online','Lun-Vier 7 am - 5 pm','2026 Mi Negocio Derecho Reservados','favicon_1772490781.png','las mejores ofertas','[{\"label\":\"Categorías\",\"path\":\"/categorias\",\"icon\":\"category\"},{\"label\":\"Productos\",\"path\":\"/productos\",\"icon\":\"inventory_2\"},{\"label\":\"Ofertas\",\"path\":\"/ofertas\",\"icon\":\"sell\"},{\"label\":\"Contáctanos\",\"path\":\"/contacto\",\"icon\":\"support_agent\"}]','[{\"title\":\"Sobre Nosotros\",\"links\":[{\"label\":\"Nuestra Historia\",\"path\":\"/nosotros\"}]},{\"title\":\"Servicio al Cliente\",\"links\":[{\"label\":\"Centro de Ayuda\",\"path\":\"/ayuda\"}]}]','Ofertas exclusivas online','Bienvenido a nuestra tienda online','Los mejores productos','Disfruta de nuestra variedad de productos tecnológicos ','hero_1772490192.jpg','Comprar Ahora','Ver Catálogo','#5559DD','#2800F0','#D8D9D3','#7CB0E4');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;

--
-- Table structure for table `departamentos_envio`
--

DROP TABLE IF EXISTS `departamentos_envio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departamentos_envio` (
  `id_departamento` int(11) NOT NULL,
  `nombre_departamento` varchar(100) NOT NULL,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_departamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos_envio`
--

/*!40000 ALTER TABLE `departamentos_envio` DISABLE KEYS */;
INSERT INTO `departamentos_envio` VALUES (1,'Atlántida',100.00),(2,'Colón',100.00),(3,'Comayagua',100.00),(4,'Copán',100.00),(5,'Cortés',100.00),(6,'Choluteca',150.00),(7,'El Paraíso',0.00),(8,'Francisco Morazán',0.00),(9,'Gracias a Dios',0.00),(10,'Intibucá',0.00),(11,'Islas de la Bahía',0.00),(12,'La Paz',0.00),(13,'Lempira',0.00),(14,'Ocotepeque',0.00),(15,'Olancho',0.00),(16,'Santa Bárbara',0.00),(17,'Valle',0.00),(18,'Yoro',0.00);
/*!40000 ALTER TABLE `departamentos_envio` ENABLE KEYS */;

--
-- Table structure for table `detalle_pedido`
--

DROP TABLE IF EXISTS `detalle_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_pedido` (
  `id_detalle` int(11) NOT NULL AUTO_INCREMENT,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tasa_impuesto` decimal(5,2) DEFAULT 0.00,
  `monto_impuesto` decimal(10,2) DEFAULT 0.00,
  `id_pedido` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_producto` (`id_producto`),
  CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedido`
--

/*!40000 ALTER TABLE `detalle_pedido` DISABLE KEYS */;
INSERT INTO `detalle_pedido` VALUES (16,1,12000.00,12000.00,15.00,1800.00,13,8),(17,3,7000.00,21000.00,15.00,3150.00,13,6),(18,1,10000.00,10000.00,15.00,1500.00,14,7),(19,1,10000.00,10000.00,15.00,1500.00,14,14),(20,1,2400.00,2400.00,15.00,360.00,15,13),(21,2,20000.00,40000.00,15.00,6000.00,16,12),(22,1,20000.00,20000.00,15.00,3000.00,17,12),(23,1,15000.00,15000.00,15.00,2250.00,18,11),(24,1,15000.00,15000.00,15.00,2250.00,19,11),(25,1,20000.00,20000.00,15.00,3000.00,20,12),(26,1,50000.00,50000.00,15.00,7500.00,21,9),(27,1,50000.00,50000.00,15.00,7500.00,22,9),(28,1,20000.00,20000.00,15.00,3000.00,23,12),(29,1,2400.00,2400.00,15.00,360.00,24,13);
/*!40000 ALTER TABLE `detalle_pedido` ENABLE KEYS */;

--
-- Table structure for table `direcciones_cliente`
--

DROP TABLE IF EXISTS `direcciones_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `direcciones_cliente` (
  `id_direccion` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `codigo_postal` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `referencia` varchar(200) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_departamento` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_direccion`),
  KEY `id_cliente` (`id_cliente`),
  KEY `fk_direcciones_departamento` (`id_departamento`),
  CONSTRAINT `direcciones_cliente_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `fk_direcciones_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos_envio` (`id_departamento`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direcciones_cliente`
--

/*!40000 ALTER TABLE `direcciones_cliente` DISABLE KEYS */;
INSERT INTO `direcciones_cliente` VALUES (5,22,'La paz','La paz','07001','98048195','casa azul','2026-03-04 20:28:54',12),(6,22,'Dos Cuadras Al Oeste De Hondutel','La paz','07001','98048195','casa azul','2026-03-05 19:29:27',6);
/*!40000 ALTER TABLE `direcciones_cliente` ENABLE KEYS */;

--
-- Table structure for table `hero_slides`
--

DROP TABLE IF EXISTS `hero_slides`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hero_slides` (
  `id_slide` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `subtitulo` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `texto_boton` varchar(100) DEFAULT NULL,
  `enlace` varchar(255) DEFAULT '#',
  `orden` int(11) DEFAULT 0,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_slide`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_slides`
--

/*!40000 ALTER TABLE `hero_slides` DISABLE KEYS */;
INSERT INTO `hero_slides` VALUES (1,'Temporada de Verano','Ofertas de verano','slide_1772490148_69a60da4213e8.jpg','comprar ahora','#ofertas',0,'activo','2026-02-24 23:26:39'),(2,'LOS MEJORES AUDIFONOS ','AUDIFONOS DE GRAN CALIDAD','slide_1772495700_69a623546e4fd.png','Ver Audífonos','#productos',0,'activo','2026-03-02 23:55:00');
/*!40000 ALTER TABLE `hero_slides` ENABLE KEYS */;

--
-- Table structure for table `historial_pedido`
--

DROP TABLE IF EXISTS `historial_pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_pedido` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `estado` varchar(50) NOT NULL,
  `comentario` varchar(200) DEFAULT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_historial`),
  KEY `id_pedido` (`id_pedido`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `historial_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`),
  CONSTRAINT `historial_pedido_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_pedido`
--

/*!40000 ALTER TABLE `historial_pedido` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_pedido` ENABLE KEYS */;

--
-- Table structure for table `lista_deseos`
--

DROP TABLE IF EXISTS `lista_deseos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lista_deseos` (
  `id_lista` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_lista`),
  UNIQUE KEY `unique_cliente_producto` (`id_cliente`,`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lista_deseos`
--

/*!40000 ALTER TABLE `lista_deseos` DISABLE KEYS */;
INSERT INTO `lista_deseos` VALUES (19,22,7,'2026-03-04 21:13:07','2026-03-04 21:13:07');
/*!40000 ALTER TABLE `lista_deseos` ENABLE KEYS */;

--
-- Table structure for table `marcas`
--

DROP TABLE IF EXISTS `marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marcas` (
  `id_marca` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `logo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_marca`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (19,'Samsung','activo','marca_1772656405_69a89715276ab.png'),(20,'Apple','activo','marca_1772656414_69a8971ed9c4b.png'),(21,'Sony','activo',NULL),(22,'LG','activo',NULL),(23,'HP','activo',NULL),(24,'Dell','activo',NULL),(25,'Lenovo','activo',NULL),(26,'Asus','activo',NULL),(27,'Hisense','activo',NULL),(28,'JBL','activo','marca_1772651881_69a88569118da.png');
/*!40000 ALTER TABLE `marcas` ENABLE KEYS */;

--
-- Table structure for table `mensajes_contacto`
--

DROP TABLE IF EXISTS `mensajes_contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensajes_contacto` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `asunto` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` enum('nuevo','leido','respondido','cerrado') DEFAULT 'nuevo',
  `fecha_mensaje` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_respuesta` timestamp NULL DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  PRIMARY KEY (`id_mensaje`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes_contacto`
--

/*!40000 ALTER TABLE `mensajes_contacto` DISABLE KEYS */;
INSERT INTO `mensajes_contacto` VALUES (6,'Cadena de plata con perlas','ea3192571@gmail.com','9804819547','direccion','fdgg','leido','2026-02-25 17:00:06',NULL,NULL);
/*!40000 ALTER TABLE `mensajes_contacto` ENABLE KEYS */;

--
-- Table structure for table `metodos_envio`
--

DROP TABLE IF EXISTS `metodos_envio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodos_envio` (
  `id_envio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `costo` decimal(10,2) DEFAULT 0.00,
  `tiempo_estimado` varchar(50) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  PRIMARY KEY (`id_envio`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_envio`
--

/*!40000 ALTER TABLE `metodos_envio` DISABLE KEYS */;
INSERT INTO `metodos_envio` VALUES (3,'rapido','ll',50.00,'2 horas','activo'),(42,'Envio moderado','',100.00,'1-5 dias','activo');
/*!40000 ALTER TABLE `metodos_envio` ENABLE KEYS */;

--
-- Table structure for table `metodos_pago`
--

DROP TABLE IF EXISTS `metodos_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodos_pago` (
  `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  PRIMARY KEY (`id_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_pago`
--

/*!40000 ALTER TABLE `metodos_pago` DISABLE KEYS */;
INSERT INTO `metodos_pago` VALUES (3,'pagar al recibir','hgfrd','activo'),(18,'Transferencia','hg','activo');
/*!40000 ALTER TABLE `metodos_pago` ENABLE KEYS */;

--
-- Table structure for table `pedidos`
--

DROP TABLE IF EXISTS `pedidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pedidos` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `subtotal` decimal(10,2) NOT NULL,
  `envio_departamento` decimal(10,2) DEFAULT 0.00,
  `impuesto_total` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','confirmado','enviado','entregado','cancelado') DEFAULT 'pendiente',
  `id_cliente` int(11) NOT NULL,
  `id_direccion` int(11) NOT NULL,
  `id_envio` int(11) NOT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `comprobante_pago` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_direccion` (`id_direccion`),
  KEY `id_envio` (`id_envio`),
  KEY `id_metodo_pago` (`id_metodo_pago`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`),
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones_cliente` (`id_direccion`),
  CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`id_envio`) REFERENCES `metodos_envio` (`id_envio`),
  CONSTRAINT `pedidos_ibfk_4` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pago` (`id_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (13,'2026-03-04 14:29:45',33000.00,0.00,4950.00,37950.00,'pendiente',22,5,3,3,NULL),(14,'2026-03-05 13:30:33',20000.00,0.00,3000.00,23000.00,'cancelado',22,6,42,3,NULL),(15,'2026-03-05 14:54:05',2400.00,0.00,360.00,2760.00,'cancelado',22,6,3,3,NULL),(16,'2026-03-05 14:56:00',40000.00,0.00,6000.00,46000.00,'cancelado',22,6,3,3,NULL),(17,'2026-03-05 14:59:26',20000.00,0.00,3000.00,23000.00,'cancelado',22,6,3,3,NULL),(18,'2026-03-05 15:01:35',15000.00,0.00,2250.00,17250.00,'cancelado',22,6,3,3,NULL),(19,'2026-03-05 15:14:06',15000.00,0.00,2250.00,17250.00,'cancelado',22,6,3,3,NULL),(20,'2026-03-05 15:14:31',20000.00,0.00,3000.00,23000.00,'cancelado',22,6,3,3,NULL),(21,'2026-03-05 15:26:25',50000.00,0.00,7500.00,57500.00,'cancelado',22,6,3,3,NULL),(22,'2026-03-05 15:28:04',50000.00,0.00,7500.00,57500.00,'cancelado',22,6,3,3,NULL),(23,'2026-03-05 15:36:59',20000.00,0.00,3000.00,23000.00,'cancelado',22,6,3,3,NULL),(24,'2026-03-07 16:16:57',2400.00,150.00,360.00,2960.00,'confirmado',22,6,3,18,'comp_69aca3d9ba088.png');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;

--
-- Table structure for table `producto_imagenes`
--

DROP TABLE IF EXISTS `producto_imagenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto_imagenes` (
  `id_imagen` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` int(11) NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL,
  `orden` int(11) DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_imagen`),
  KEY `fk_producto_imagen` (`id_producto`),
  CONSTRAINT `fk_producto_imagen` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto_imagenes`
--

/*!40000 ALTER TABLE `producto_imagenes` DISABLE KEYS */;
INSERT INTO `producto_imagenes` VALUES (11,6,'img/productos/prod_6_1772490313_0.png',1,'2026-03-02 22:25:13'),(12,7,'img/productos/prod_7_1772490969_0.png',1,'2026-03-02 22:36:09'),(13,7,'img/productos/prod_7_1772490969_1.png',2,'2026-03-02 22:36:09'),(14,7,'img/productos/prod_7_1772490969_2.png',3,'2026-03-02 22:36:09'),(15,7,'img/productos/prod_7_1772490969_3.png',4,'2026-03-02 22:36:09'),(16,8,'img/productos/prod_8_1772493093_0.png',1,'2026-03-02 23:11:33'),(17,8,'img/productos/prod_8_1772493093_1.png',2,'2026-03-02 23:11:33'),(18,8,'img/productos/prod_8_1772493093_2.png',3,'2026-03-02 23:11:33'),(19,9,'img/productos/prod_9_1772493580_0.png',1,'2026-03-02 23:19:40'),(20,10,'img/productos/prod_10_1772493684_0.png',1,'2026-03-02 23:21:24'),(21,11,'img/productos/prod_11_1772493848_0.png',1,'2026-03-02 23:24:08'),(22,12,'img/productos/prod_12_1772493950_0.png',1,'2026-03-02 23:25:50'),(23,13,'img/productos/prod_13_1772494401_0.png',1,'2026-03-02 23:33:21'),(24,14,'img/productos/prod_14_1772653345_0.png',1,'2026-03-04 19:42:25');
/*!40000 ALTER TABLE `producto_imagenes` ENABLE KEYS */;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `estado` enum('disponible','agotado') DEFAULT 'disponible',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_categoria` int(11) NOT NULL,
  `id_marca` int(11) NOT NULL,
  `precio_descuento` decimal(10,2) DEFAULT NULL,
  `en_oferta` tinyint(1) DEFAULT 0,
  `fecha_inicio_oferta` date DEFAULT NULL,
  `fecha_fin_oferta` date DEFAULT NULL,
  PRIMARY KEY (`id_producto`),
  UNIQUE KEY `codigo` (`codigo`),
  KEY `id_categoria` (`id_categoria`),
  KEY `id_marca` (`id_marca`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `productos_ibfk_2` FOREIGN KEY (`id_marca`) REFERENCES `marcas` (`id_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (6,'3030','Monitor Dell/ 27\"/ FHD/ Blanco','Monitor FHD de 68,60 cm (27\") que optimiza la comodidad ocular y ofrece imágenes impresionantes, con una acústica excepcional y un diseño que complementa tu estilo de vida.',7000.00,47,'disponible','2026-03-02 22:25:13',19,24,NULL,0,NULL,NULL),(7,'8787','Monitor HP 24\"/ FHD/ Blanco','El HP Series 5 23.8 ″ FHD White Monitor (modelo 524sw, SKU 94C21AA#ABA) es una pantalla IPS de alta calidad diseñada para brindar imágenes nítidas y cómodas en cualquier espacio. Con una resolución Full HD de 1920 × 1080, una tasa de refresco de hasta 100 Hz, y un tiempo de respuesta de 5 ms (con overdrive), ofrece una experiencia visual fluida y detallada, ideal para productividad, entretenimiento e incluso gaming ligero.\r\n\r\nCuenta con un diseño delgado de tres bordes casi sin marco, que facilita una configuración multitarea con varias pantallas y reduce el desorden con una administración simplificada de cables. Además, incorpora el certificado HP Eye Ease, que limita la luz azul sin sacrificar la precisión del color, ayudando a reducir la fatiga visual durante sesiones prolongadas.\r\n\r\nPensando en el usuario, la pantalla ofrece ajuste de inclinación y acceso a la guía ergonómica desde el software HP Display Center, para una experiencia personalizada y cómoda. Sus opciones de conectividad incluyen los puertos HDMI (1.4) y VGA.\r\n\r\nFinalmente, su diseño es respetuoso con el medio ambiente: está fabricada con materiales reciclados (incluido plástico procedente de océanos) y cuenta con embalaje totalmente sostenible. Además, está certificada por EPEAT Silver y ENERGY STAR.',10000.00,49,'disponible','2026-03-02 22:36:09',20,23,NULL,0,NULL,NULL),(8,'5050','Monitor Samsung 27\" FHD Curvo/ LS27D366','Vive una experiencia envolvente con el monitor curvo de 27\" FHD, diseñado para quienes exigen una visualización fluida, cómoda y llena de detalle. Su curvatura te acerca a la acción, mejorando la inmersión en juegos, series y multitarea, mientras su resolución 1920x1080 ofrece textos nítidos y colores vibrantes para un día a día más productivo.\r\n\r\nGracias a su panel optimizado para reducir reflejos y fatiga visual, podrás trabajar durante horas con mayor confort. Su diseño elegante y minimalista se integra a cualquier escritorio, y la navegación es ágil gracias a tecnologías que suavizan el movimiento para una sensación más natural en desplazamientos y escenas rápidas.\r\n\r\nPantalla curva 27\" FHD (1920x1080): mayor inmersión y enfoque natural.\r\nColores intensos y buen contraste: ideal para contenido multimedia y creatividad.\r\nMovimiento más fluido: perfecto para juegos casuales y video sin interrupciones.\r\nComodidad visual: modos que ayudan a disminuir la fatiga y el parpadeo.\r\nDiseño delgado y moderno: base estable y bordes finos para más espacio útil.\r\nConectividad versátil: entradas listas para PC, consolas y laptops.\r\nEleva tu setup con un monitor que combina estilo, rendimiento y confort. Ya sea para estudiar, crear o entretenerte, este 27\" curvo FHD transforma cada momento frente a la pantalla en una experiencia increíble.',12000.00,9,'disponible','2026-03-02 23:11:33',10,19,NULL,0,NULL,NULL),(9,'101010','Apple iPhone 17 Pro/ Versión SIM/ 12GB RAM/ 256GB/ Silver','Descubre el Apple iPhone 17 Pro en su elegante acabado Silver, diseñado para quienes exigen velocidad, estilo y experiencias móviles superiores. Con 12GB de RAM para multitarea sin límites y 256GB de almacenamiento para tus fotos, apps y contenido 4K, este iPhone eleva cada interacción con una fluidez impresionante y una batería optimizada para el día a día.\r\n\r\nSu sistema de cámaras Pro captura detalles nítidos y colores vibrantes, ideal para contenido social, fotografía nocturna y video profesional. La pantalla avanzada ofrece brillo alto y contraste profundo para ver series, jugar y trabajar con una calidad visual envolvente. Conectividad estable, audio inmersivo y un chasis premium que combina ligereza y resistencia completan una experiencia top.\r\n\r\nMemoria: 12GB RAM para apps exigentes y gaming fluido.\r\nAlmacenamiento: 256GB para bibliotecas de fotos, videos y documentos.\r\nPantalla Pro: alta tasa de refresco y colores precisos para una visualización de nivel estudio.\r\nCámaras Pro: fotos con gran rango dinámico, retratos impactantes y video estable, incluso de noche.\r\nDiseño Silver: acabado sofisticado con materiales de calidad y agarre cómodo.\r\nBatería inteligente: autonomía optimizada y carga rápida para seguir tu ritmo.\r\nSeguridad y privacidad: autenticación biométrica avanzada y protección de datos.\r\nSi buscas rendimiento premium, estética impecable y herramientas creativas potentes, el iPhone 17 Pro Silver 256GB es la elección perfecta para destacar en cada momento.',50000.00,99,'disponible','2026-03-02 23:19:40',9,20,NULL,0,NULL,NULL),(10,'50500','Apple iPhone 13/ 4GB RAM/ 128GB/ Midnight','El Apple iPhone 13 de 128GB en color Midnight combina diseño elegante con un rendimiento excepcional. Su pantalla Super Retina XDR de 6.1 pulgadas ofrece colores vibrantes, brillo superior y una experiencia visual inmersiva ideal para ver series, editar fotos o jugar.\r\nEquipado con el chip A15 Bionic, brinda potencia y eficiencia para todo lo que hacés: desde multitarea fluida hasta juegos exigentes y grabación en calidad cinematográfica. Su sistema de cámara dual avanzada de 12MP con modo Noche, HDR Inteligente y grabación en 4K permite capturar fotos y videos con detalles impresionantes.\r\nEl iPhone 13 también ofrece mayor duración de batería, resistencia al agua y polvo (IP68) y conectividad 5G para descargas ultrarrápidas. Además, cuenta con Face ID y la última versión de iOS, garantizando seguridad, fluidez y actualizaciones continuas.\r\nEl iPhone 13 Midnight de 128GB es la combinación perfecta entre potencia, diseño premium y tecnología Apple, ideal para quienes buscan una experiencia móvil sin límites.',15000.00,50,'disponible','2026-03-02 23:21:24',17,20,10500.00,1,'2026-03-02','2026-03-07'),(11,'789','Smart TV Hisense 58\" UHD VIDAA 4K/ HIS-58A6NV','El Hisense Smart TV UHD de 58 pulgadas modelo 58A6NV transforma tu sala en un verdadero centro de entretenimiento. Su resolución Ultra HD 4K brinda imágenes cuatro veces más nítidas que un televisor Full HD, con colores realistas y gran nivel de detalle en cada escena.\r\nGracias al sistema operativo VIDAA, disfrutás una navegación ágil e intuitiva con acceso directo a aplicaciones de streaming como Netflix, Prime Video, Disney+ y YouTube. Además, su diseño moderno y delgado se adapta a cualquier espacio, aportando estilo y elegancia.\r\nEl Hisense 58A6NV también ofrece conectividad avanzada para vincular consolas de videojuegos, dispositivos de audio y más, garantizando una experiencia completa tanto para cine en casa como para gaming.\r\nSi buscás un televisor de gran tamaño, con excelente relación calidad-precio y funciones inteligentes, el Hisense UHD 58” 58A6NV es la elección perfecta.',15000.00,10,'disponible','2026-03-02 23:24:08',24,27,NULL,0,NULL,NULL),(12,'6363','Smart TV Hisense 65\" QLED VIDAA 4K/ HIS-65Q6QV','Disfruta un salto real en entretenimiento con el Televisor QLED de 65” 4K. Sus colores vibrantes, negros más profundos y brillo optimizado transforman tus series, películas y videojuegos con una claridad impresionante. Con VIDAA Smart TV navegas de forma ágil por tus apps favoritas, mientras el control por voz simplifica cada comando.\r\n\r\nLa tecnología QLED potencia el volumen de color para imágenes más vivas, y el 4K Ultra HD ofrece detalle fino en cada escena. Su modo de imagen inteligente ajusta el contenido en tiempo real, y el modo Juego reduce el input lag para reacciones rápidas. Además, su diseño de bisel delgado maximiza la pantalla y luce moderno en cualquier espacio.\r\n\r\nPantalla: 65” QLED, resolución 4K Ultra HD\r\nSmart TV: plataforma VIDAA rápida e intuitiva\r\nColores: alto volumen de color para imágenes más realistas\r\nHDR: compatibilidad con formatos populares para mayor contraste\r\nGaming: modo Juego con baja latencia para respuestas inmediatas\r\nAudio: sonido envolvente optimizado para cine, música y deportes\r\nConectividad: Wi‑Fi, múltiples HDMI y USB para tus dispositivos\r\nAsistentes: control por voz y búsqueda inteligente\r\nDiseño: bisel delgado y soporte estable para una estética elegante\r\nPrepárate para una experiencia inmersiva que combina calidad de imagen, facilidad de uso y rendimiento. Este Smart TV lo tiene todo para convertir tu sala en tu destino favorito de entretenimiento.',20000.00,15,'disponible','2026-03-02 23:25:50',24,27,NULL,0,NULL,NULL),(13,'258','Audífonos JBL/ Sense Lite/ Inalámbrico/ Negro','Descubre los OpenEar SenseLite Black, audífonos de diseño abierto que elevan tu día a día con sonido nítido, comodidad ultraligera y una sensación natural de libertad. Su arquitectura Open-Ear te permite disfrutar tu playlist favorita mientras mantienes total conciencia del entorno, ideal para entrenar al aire libre, movilizarte en la ciudad o trabajar con enfoque.\r\n\r\nCon un ajuste estable y ergonómico, estos audífonos se mantienen en su lugar durante tus movimientos más intensos. Disfruta de Bluetooth confiable, llamadas claras y una batería pensada para acompañarte por horas. Su diseño minimalista en color negro combina con cualquier estilo y su control intuitivo te da acceso rápido a música, volumen y llamadas.\r\n\r\nDiseño Open-Ear: escucha tu entorno sin sacrificar calidad de audio.\r\nComodidad ultraligera: uso prolongado sin presión en el canal auditivo.\r\nConectividad Bluetooth estable: emparejamiento rápido y transmisión fluida.\r\nLlamadas nítidas: micrófonos optimizados para voces más claras.\r\nControles sencillos: gestiona música y llamadas al instante.\r\nEstilo negro premium: discreto, moderno y resistente para el día a día.\r\nAutonomía para la jornada: horas de reproducción para entrenamiento, trabajo y ocio.\r\nSi buscas libertad, confort y sonido que te acompaña en todo momento, los SenseLite Black son la elección perfecta. Conecta, muévete y siente tu música como nunca, sin aislarte del mundo.',3000.00,8,'disponible','2026-03-02 23:33:21',26,28,2400.00,1,'2026-03-02','2026-03-14'),(14,'3031','aplee computadora','dsafds',10000.00,12,'disponible','2026-03-04 19:42:25',11,20,NULL,0,NULL,NULL);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'admin','Administrador del sistema'),(2,'vendedor','Gestión de pedidos'),(3,'cliente','Cliente de la tienda');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

--
-- Table structure for table `tipos_cuenta_banco`
--

DROP TABLE IF EXISTS `tipos_cuenta_banco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_cuenta_banco` (
  `id_tipo_cuenta` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_tipo_cuenta`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_cuenta_banco`
--

/*!40000 ALTER TABLE `tipos_cuenta_banco` DISABLE KEYS */;
INSERT INTO `tipos_cuenta_banco` VALUES (1,'Cuenta de Ahorro'),(2,'Cuenta Corriente'),(3,'Cuenta Empresarial');
/*!40000 ALTER TABLE `tipos_cuenta_banco` ENABLE KEYS */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contraseña` varchar(255) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_rol` int(11) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (7,'eduardo avila12','ea31925712@gmail.com','$2y$10$zp9Olr3n1B4wzEAx/x/KPuA4X/w57ofm51gLbg78ZMHfdwRmIoPSO','activo','2026-02-16 02:10:19',1),(13,'Eduardo1201','ea319257166@gmail.com','$2y$10$36vlWeiRgGcwbL8NBX6mIObuA9T9DAn3nnSSoMfaX4nwTTZUn..PO','activo','2026-02-16 04:17:30',2),(33,'edurado1201','ea3192571@gmail.com','$2y$10$angZBbCZWdV7Q2nzKo4WPuziGil0XtndpHs06FxNyWT.sS61fjQ5u','activo','2026-03-03 06:23:00',3);
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;

--
-- Dumping routines for database 'negocio_web'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-07 16:48:05
