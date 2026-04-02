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
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario que realizó la acción',
  `usuario_nombre` varchar(255) NOT NULL COMMENT 'Nombre del usuario que actuó',
  `usuario_rol` int(11) DEFAULT NULL COMMENT 'Rol del usuario (1=Admin, 2=Vendedor, 3=Cliente)',
  `accion` varchar(50) NOT NULL COMMENT 'Tipo de acción (CREATE, UPDATE, DELETE, LOGIN, etc)',
  `tabla` varchar(100) NOT NULL COMMENT 'Tabla afectada',
  `registro_id` int(11) NOT NULL COMMENT 'ID del registro modificado',
  `valores_anteriores` longtext DEFAULT NULL COMMENT 'JSON con valores antes del cambio',
  `valores_nuevos` longtext DEFAULT NULL COMMENT 'JSON con valores después del cambio',
  `notas` varchar(500) DEFAULT NULL COMMENT 'Notas adicionales',
  `ip` varchar(45) NOT NULL COMMENT 'IP del cliente',
  `navegador` varchar(255) DEFAULT NULL COMMENT 'Información del navegador/user agent',
  `tiempo` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Cuándo ocurrió',
  PRIMARY KEY (`id`),
  KEY `idx_usuario_id` (`usuario_id`),
  KEY `idx_usuario_nombre` (`usuario_nombre`),
  KEY `idx_accion` (`accion`),
  KEY `idx_tabla` (`tabla`),
  KEY `idx_registro_id` (`registro_id`),
  KEY `idx_tiempo` (`tiempo`),
  KEY `idx_accion_tabla` (`accion`,`tabla`),
  KEY `idx_usuario_tiempo` (`usuario_id`,`tiempo`),
  KEY `idx_tabla_registro` (`tabla`,`registro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de auditoría de todas las acciones sensitivas del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_logs`
--

/*!40000 ALTER TABLE `audit_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_logs` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bancos`
--

/*!40000 ALTER TABLE `bancos` DISABLE KEYS */;
INSERT INTO `bancos` VALUES (2,'BI Banpais','210560063358',3,'1772866930_unnamed.png'),(3,'Bac','7485885646',3,'1772867980_Logo-BAC.png');
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (3,'Los mejores siempre','Todos los productos en descuento','banner_1774143842_69bf496273497.png','Prueba','',1,'activo','2026-03-02 06:35:34');
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
  CONSTRAINT `carrito_detalle_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carritos` (`id_carrito`) ON DELETE CASCADE,
  CONSTRAINT `carrito_detalle_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carrito_detalle`
--

/*!40000 ALTER TABLE `carrito_detalle` DISABLE KEYS */;
INSERT INTO `carrito_detalle` VALUES (111,75,8,0,600.00,0.00);
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
  CONSTRAINT `carritos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carritos`
--

/*!40000 ALTER TABLE `carritos` DISABLE KEYS */;
INSERT INTO `carritos` VALUES (11,18,'2026-03-01 18:45:11','comprado'),(12,18,'2026-03-02 00:55:22','comprado'),(13,18,'2026-03-03 22:58:48','comprado'),(14,18,'2026-03-04 00:41:46','comprado'),(15,18,'2026-03-04 00:50:38','comprado'),(16,18,'2026-03-04 00:58:50','comprado'),(17,18,'2026-03-04 01:02:12','comprado'),(18,18,'2026-03-04 21:59:22','comprado'),(19,18,'2026-03-04 22:29:24','comprado'),(20,18,'2026-03-04 22:57:55','comprado'),(21,18,'2026-03-04 23:22:13','comprado'),(22,18,'2026-03-04 23:22:51','comprado'),(23,18,'2026-03-04 23:34:40','comprado'),(24,18,'2026-03-04 23:36:24','comprado'),(25,18,'2026-03-04 23:41:41','comprado'),(26,18,'2026-03-04 23:42:14','comprado'),(27,18,'2026-03-04 23:43:30','comprado'),(28,18,'2026-03-04 23:45:40','comprado'),(29,18,'2026-03-05 00:04:51','comprado'),(30,18,'2026-03-05 00:07:41','comprado'),(31,18,'2026-03-05 00:09:35','comprado'),(32,18,'2026-03-05 00:14:13','comprado'),(33,18,'2026-03-05 00:16:42','comprado'),(34,18,'2026-03-05 00:28:16','comprado'),(35,18,'2026-03-05 00:39:34','comprado'),(36,18,'2026-03-05 00:51:23','comprado'),(37,18,'2026-03-05 00:51:42','comprado'),(38,18,'2026-03-05 00:52:40','comprado'),(39,18,'2026-03-05 00:58:29','comprado'),(40,18,'2026-03-05 01:03:07','comprado'),(41,18,'2026-03-06 20:53:12','comprado'),(42,18,'2026-03-06 21:26:01','comprado'),(43,18,'2026-03-06 23:19:16','comprado'),(44,18,'2026-03-06 23:34:56','comprado'),(45,18,'2026-03-06 23:39:49','comprado'),(46,18,'2026-03-06 23:41:23','comprado'),(47,18,'2026-03-06 23:42:07','comprado'),(48,18,'2026-03-07 22:23:42','comprado'),(49,18,'2026-03-07 22:36:08','comprado'),(50,18,'2026-03-08 18:56:25','comprado'),(51,18,'2026-03-09 19:20:27','comprado'),(52,18,'2026-03-11 23:35:49','comprado'),(53,18,'2026-03-11 23:53:55','comprado'),(54,18,'2026-03-11 23:54:52','comprado'),(55,18,'2026-03-12 23:42:13','comprado'),(56,18,'2026-03-12 23:43:00','comprado'),(57,18,'2026-03-12 23:50:15','comprado'),(58,18,'2026-03-12 23:51:33','comprado'),(59,18,'2026-03-13 00:02:21','comprado'),(60,18,'2026-03-13 00:14:41','comprado'),(61,18,'2026-03-14 21:39:40','comprado'),(62,18,'2026-03-14 23:07:31','comprado'),(63,18,'2026-03-15 16:41:01','comprado'),(64,18,'2026-03-15 16:41:10','comprado'),(65,18,'2026-03-15 16:41:19','comprado'),(66,18,'2026-03-15 16:41:29','comprado'),(67,18,'2026-03-15 16:41:47','comprado'),(68,18,'2026-03-15 16:41:57','comprado'),(69,18,'2026-03-15 16:42:05','comprado'),(70,18,'2026-03-15 16:42:14','comprado'),(71,18,'2026-03-15 16:42:40','comprado'),(72,18,'2026-03-15 16:42:52','comprado'),(73,18,'2026-03-15 16:43:01','comprado'),(74,18,'2026-03-15 16:54:35','activo'),(75,24,'2026-03-16 20:06:16','activo');
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
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (16,NULL,'Futbol','','activo','fa-futbol',15.00),(17,16,'Calzado (Taco y tenis para futbol)','','activo','',15.00),(18,NULL,'Gimnasio','','activo','fa-dumbbell',15.00),(19,18,'Pesas y mancuernas','','activo','',15.00),(20,NULL,'Tenis','','activo','',15.00),(21,20,'Raquetas de tenis','','activo','',15.00);
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
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cliente`),
  KEY `fk_clientes_usuarios` (`id_usuario`),
  CONSTRAINT `fk_clientes_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_cimb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (18,32,'Jonathan Orellana','activo','2026-03-02 00:42:11'),(19,34,'Daniel Martinez','activo','2026-03-02 07:24:22'),(23,38,'Ruth Orellana','activo','2026-03-08 05:27:47'),(24,39,'EDUARDO AVILA','activo','2026-03-17 02:06:13');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;

--
-- Table structure for table `compras`
--

DROP TABLE IF EXISTS `compras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `compras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor` varchar(100) NOT NULL,
  `fecha` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compras`
--

/*!40000 ALTER TABLE `compras` DISABLE KEYS */;
INSERT INTO `compras` VALUES (1,'7 GYM','2026-03-12 23:14:29'),(2,'7 GYM','2026-03-15 16:40:20'),(3,'7 GYM','2026-03-15 16:40:32'),(4,'7 GYM','2026-03-15 16:40:42');
/*!40000 ALTER TABLE `compras` ENABLE KEYS */;

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
INSERT INTO `configuracion` VALUES (1,'Tienda de Valentina','logo_1774143228.png','98988460','jonathan.medez@uph.hn.edu','C3RC+JJQ, La Lima, Cortés','Aquí encontrarás todo lo que necesitas para entrenar, competir y superar tus límites. Trabajamos con productos de alta calidad para acompañarte en cada paso de tu camino deportivo, ya seas principiante o atleta de alto rendimiento.\r\nEquípate con lo mejor.\r\nEntrena con actitud.\r\nRinde como un elite. 💪','{\"facebook\":\"\",\"instagram\":\"\",\"whatsapp\":\"\",\"tiktok\":\"\",\"twitter\":\"\",\"youtube\":\"\"}','HNL','Donde nace tu mejor version','8:00 am- 9:00 pm','Tu tienda de confianza en artículos deportivos. Calidad, rendimiento y estilo para cada disciplina.\r\n© 2026 Elite Sport. Todos los derechos reservados.',NULL,'Equípate para entrenar, competir y ganar 💪','[{\"label\":\"Categorías\",\"path\":\"/categorias\"},{\"label\":\"Ofertas\",\"path\":\"/ofertas\"},{\"label\":\"Contáctanos\",\"path\":\"/contacto\"},{\"label\":\"Productos\",\"path\":\"/productos\",\"icon\":\"inventory_2\"}]','[{\"title\":\"Sobre Nosotros\",\"links\":[{\"label\":\"Nuestra Historia\",\"path\":\"/nosotros\"},{\"label\":\"Bolsa de Trabajo\",\"path\":\"/empleos\"},{\"label\":\"Sostenibilidad\",\"path\":\"/sustentabilidad\"}]},{\"title\":\"Servicio al Cliente\",\"links\":[{\"label\":\"Centro de Ayuda\",\"path\":\"/ayuda\"},{\"label\":\"Políticas de Envío\",\"path\":\"/envios\"},{\"label\":\"Devoluciones\",\"path\":\"/devoluciones\"}]}]','Elite Sport','Donde nace tu mejor versión','','En Elite Sport creemos que el rendimiento comienza con la actitud correcta y el equipo adecuado.','hero_1774143811.png','Comprar Ahora','Ver Catálogo','#E600DE','#518CC8','#F6F7F8','#101922');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;

--
-- Table structure for table `departamentos_envio`
--

DROP TABLE IF EXISTS `departamentos_envio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departamentos_envio` (
  `id_departamento` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_departamento` varchar(100) NOT NULL,
  `costo_envio` decimal(10,2) NOT NULL DEFAULT 0.00,
  `dias_entrega` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departamentos_envio`
--

/*!40000 ALTER TABLE `departamentos_envio` DISABLE KEYS */;
INSERT INTO `departamentos_envio` VALUES (3,'Comayagua',100.00,1),(4,'Copán',100.00,1),(5,'Cortés',100.00,1),(7,'El Paraíso',150.00,1),(8,'Francisco Morazán',300.00,1),(9,'Gracias a Dios',600.00,1),(10,'Intibucá',200.00,1),(11,'Islas de la Bahía',60.00,1),(12,'La Paz',100.00,1),(13,'Lempira',120.00,1),(14,'Ocotepeque',100.00,1),(15,'Olancho',50.00,1),(16,'Santa Bárbara',170.00,1),(17,'Valle',250.00,1),(18,'Yoro',320.00,1),(20,'Choluteca',150.00,3),(23,'Atlantida',300.00,5),(24,'Colon',150.00,4);
/*!40000 ALTER TABLE `departamentos_envio` ENABLE KEYS */;

--
-- Table structure for table `detalle_compra`
--

DROP TABLE IF EXISTS `detalle_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `fk_compra` (`compra_id`),
  KEY `fk_producto` (`producto_id`),
  CONSTRAINT `fk_compra` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`),
  CONSTRAINT `fk_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_compra`
--

/*!40000 ALTER TABLE `detalle_compra` DISABLE KEYS */;
INSERT INTO `detalle_compra` VALUES (1,1,8,5,100.00),(2,2,8,5,100.00),(3,3,7,5,200.00),(4,4,6,5,120.00);
/*!40000 ALTER TABLE `detalle_compra` ENABLE KEYS */;

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
  CONSTRAINT `detalle_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `detalle_pedido_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_pedido`
--

/*!40000 ALTER TABLE `detalle_pedido` DISABLE KEYS */;
INSERT INTO `detalle_pedido` VALUES (66,1,600.00,600.00,15.00,90.00,68,8),(67,1,1000.00,1000.00,15.00,150.00,69,7),(68,1,3500.00,3500.00,15.00,525.00,70,6),(69,1,600.00,600.00,15.00,90.00,71,8),(70,1,1000.00,1000.00,15.00,150.00,72,7),(71,1,600.00,600.00,15.00,90.00,73,8),(72,1,1000.00,1000.00,15.00,150.00,74,7),(73,1,3500.00,3500.00,15.00,525.00,75,6),(74,1,1000.00,1000.00,15.00,150.00,76,7),(75,1,1000.00,1000.00,15.00,150.00,77,7),(76,1,600.00,600.00,15.00,90.00,78,8),(77,1,600.00,600.00,15.00,90.00,79,8);
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
  `activo` tinyint(1) DEFAULT 1,
  `fecha_eliminacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id_direccion`),
  KEY `id_cliente` (`id_cliente`),
  KEY `fk_departamento` (`id_departamento`),
  CONSTRAINT `direcciones_cliente_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  CONSTRAINT `fk_departamento` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos_envio` (`id_departamento`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `direcciones_cliente`
--

/*!40000 ALTER TABLE `direcciones_cliente` DISABLE KEYS */;
INSERT INTO `direcciones_cliente` VALUES (4,18,'Col. los maestros 3ra calle','La Lima, Cortes','21103','98988460','Casa color roja','2026-03-02 06:46:41',5,1,NULL),(5,18,'Col. Sitraterco casa #184','La Lima, Cortes','21103','98988460','Casa color amarillo','2026-03-04 05:08:55',9,1,NULL),(6,18,'Col. Sitraterco casa #183','La Lima','21102','98988467','Casa color azul','2026-03-04 05:54:41',12,0,'2026-03-15 17:32:38'),(9,24,'honduras la paz','La paz','','98048195','casa azul','2026-03-17 02:06:57',12,1,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hero_slides`
--

/*!40000 ALTER TABLE `hero_slides` DISABLE KEYS */;
INSERT INTO `hero_slides` VALUES (2,'Bannes promocionales','Los mejores productos','slide_1774163122_69bf94b20f330.png','','',1,'activo','2026-03-02 06:24:38'),(3,'Ven y compueba nuestra calidad','Los mejores productos','slide_1774163132_69bf94bc08947.png','Prueba','#ofertas',2,'activo','2026-03-02 06:24:53'),(4,'Productos en ofertas','Descuentos en todos los productos','slide_1774163110_69bf94a6d5eb6.png','Ofertas','#ofertas',0,'activo','2026-03-02 07:13:20');
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
  CONSTRAINT `historial_pedido_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id_pedido`) ON DELETE CASCADE,
  CONSTRAINT `historial_pedido_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
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
  UNIQUE KEY `unique_cliente_producto` (`id_cliente`,`id_producto`),
  KEY `fk_lista_deseos_producto` (`id_producto`),
  CONSTRAINT `fk_lista_deseos_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  CONSTRAINT `fk_lista_deseos_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lista_deseos`
--

/*!40000 ALTER TABLE `lista_deseos` DISABLE KEYS */;
/*!40000 ALTER TABLE `lista_deseos` ENABLE KEYS */;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) NOT NULL COMMENT 'Dirección IP del cliente (IPv4 o IPv6)',
  `usuario` varchar(100) DEFAULT NULL COMMENT 'Nombre de usuario intentado',
  `intentos_exitosos` tinyint(1) DEFAULT 0 COMMENT '1=login exitoso, 0=fallido',
  `razon` varchar(255) DEFAULT 'intento_fallido' COMMENT 'Razón del fallo',
  `tiempo` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Cuándo ocurrió el intento',
  `bloqueado_hasta` datetime DEFAULT NULL COMMENT 'Si está bloqueado, cuándo se desbloquea',
  PRIMARY KEY (`id`),
  KEY `idx_ip` (`ip`),
  KEY `idx_usuario` (`usuario`),
  KEY `idx_tiempo` (`tiempo`),
  KEY `idx_bloqueado` (`bloqueado_hasta`),
  KEY `idx_ip_tiempo` (`ip`,`tiempo`),
  KEY `idx_usuario_tiempo` (`usuario`,`tiempo`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de intentos de login para rate limiting y seguridad';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
INSERT INTO `login_attempts` VALUES (1,'::1','ea3192571@gmail.com',1,'login_exitoso','2026-03-22 00:04:04',NULL),(2,'::1','jonathan.medez@uph.edu.hn',1,'login_exitoso','2026-03-22 00:05:29',NULL),(3,'::1','jonathan.medez@uph.edu.hn',1,'login_exitoso','2026-03-22 00:10:41',NULL),(4,'::1','admin@empresa.com',1,'login_exitoso','2026-03-22 00:30:01',NULL),(5,'::1','admin@empresa.com',1,'login_exitoso','2026-03-22 01:08:48',NULL),(6,'::1','jonathan.medez@uph.edu.hn',1,'login_exitoso','2026-03-22 01:29:07',NULL),(7,'::1','ea3192571@gmail.com',0,'credenciales_invalidas','2026-03-22 01:32:06','2026-03-22 01:47:06'),(8,'::1','vendedor@empresa.com',1,'login_exitoso','2026-03-22 02:08:37',NULL),(9,'::1','admin@empresa.com',1,'login_exitoso','2026-03-22 02:31:01',NULL),(10,'::1','admin@empresa.com',1,'login_exitoso','2026-03-22 02:40:03',NULL),(11,'::1','ea3192571@gmail.com',0,'credenciales_invalidas','2026-03-22 02:44:35','2026-03-22 02:59:35'),(12,'::1','ea3192571@gmail.com',0,'credenciales_invalidas','2026-03-22 02:45:29','2026-03-22 03:00:29'),(13,'::1','admin@empresa.com',1,'login_exitoso','2026-03-22 02:46:45',NULL),(14,'::1','admin@empresa.com',0,'credenciales_invalidas','2026-03-22 07:38:45','2026-03-22 07:53:45'),(15,'::1','admin@empresa.com',1,'login_exitoso','2026-03-22 07:38:49',NULL),(16,'::1','admin@empresa.com',1,'login_exitoso','2026-03-22 08:03:28',NULL),(17,'::1','ea3192571@gmail.com',1,'login_exitoso','2026-03-22 08:23:58',NULL);
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;

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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marcas`
--

/*!40000 ALTER TABLE `marcas` DISABLE KEYS */;
INSERT INTO `marcas` VALUES (17,'NIke','activo','marca_1774161570_69bf8ea2515f3.png'),(18,'Adidas','activo','marca_1774161557_69bf8e95ee420.png');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes_contacto`
--

/*!40000 ALTER TABLE `mensajes_contacto` DISABLE KEYS */;
INSERT INTO `mensajes_contacto` VALUES (7,'Jonathan Orellana','jonathan.mendez@uph.edu.hn','98988467','Prueba de Contacto','Mensaje de prueba para verificar que todo funcione bien','nuevo','2026-03-02 07:25:36',NULL,NULL);
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
  `reduccion_dias` int(11) NOT NULL DEFAULT 0,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  PRIMARY KEY (`id_envio`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_envio`
--

/*!40000 ALTER TABLE `metodos_envio` DISABLE KEYS */;
INSERT INTO `metodos_envio` VALUES (46,'Envio Express','',200.00,0,'activo');
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_pago`
--

/*!40000 ALTER TABLE `metodos_pago` DISABLE KEYS */;
INSERT INTO `metodos_pago` VALUES (19,'Contra Entrega','Pagas al recibir el producto','activo'),(20,'Transferencia o deposito','A continuacion veras la imagen de nuestras cuentas bancarias','activo');
/*!40000 ALTER TABLE `metodos_pago` ENABLE KEYS */;

--
-- Table structure for table `monitoring_alerts`
--

DROP TABLE IF EXISTS `monitoring_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `monitoring_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL COMMENT 'Tipo de alerta (ACCESO_DENEGADO, DELETE_MASA, CAMBIO_ADMIN, etc)',
  `severidad` varchar(20) NOT NULL COMMENT 'Nivel de severidad (CRÍTICA, ALTA, MEDIA, BAJA)',
  `titulo` varchar(255) NOT NULL COMMENT 'Título descriptivo de la alerta',
  `mensaje` text NOT NULL COMMENT 'Mensaje detallado de la alerta',
  `detalles` longtext DEFAULT NULL COMMENT 'Detalles técnicos en JSON',
  `tiempo` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Cuándo se generó la alerta',
  `leida` tinyint(1) DEFAULT 0 COMMENT '0=no leída, 1=leída',
  PRIMARY KEY (`id`),
  KEY `idx_tipo` (`tipo`),
  KEY `idx_severidad` (`severidad`),
  KEY `idx_tiempo` (`tiempo`),
  KEY `idx_leida` (`leida`),
  KEY `idx_severidad_tiempo` (`severidad`,`tiempo`),
  KEY `idx_leida_tiempo` (`leida`,`tiempo`),
  KEY `idx_tipo_severidad` (`tipo`,`severidad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de alertas generadas por el sistema de monitoreo automático';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `monitoring_alerts`
--

/*!40000 ALTER TABLE `monitoring_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `monitoring_alerts` ENABLE KEYS */;

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
  `id_envio` int(11) DEFAULT NULL,
  `id_metodo_pago` int(11) NOT NULL,
  `comprobante_pago` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_direccion` (`id_direccion`),
  KEY `id_envio` (`id_envio`),
  KEY `id_metodo_pago` (`id_metodo_pago`),
  CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE CASCADE,
  CONSTRAINT `pedidos_ibfk_2` FOREIGN KEY (`id_direccion`) REFERENCES `direcciones_cliente` (`id_direccion`),
  CONSTRAINT `pedidos_ibfk_3` FOREIGN KEY (`id_envio`) REFERENCES `metodos_envio` (`id_envio`) ON DELETE SET NULL,
  CONSTRAINT `pedidos_ibfk_4` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pago` (`id_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedidos`
--

/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES (68,'2026-03-15 16:40:57',600.00,100.00,90.00,790.00,'pendiente',18,6,NULL,19,NULL),(69,'2026-03-15 16:41:07',1000.00,600.00,150.00,1750.00,'pendiente',18,5,NULL,19,NULL),(70,'2026-03-15 16:41:16',3500.00,100.00,525.00,4125.00,'pendiente',18,4,NULL,19,NULL),(71,'2026-03-15 16:41:26',600.00,100.00,90.00,790.00,'pendiente',18,6,NULL,19,NULL),(72,'2026-03-15 16:41:44',1000.00,100.00,150.00,1250.00,'pendiente',18,6,NULL,19,NULL),(73,'2026-03-15 16:41:53',600.00,600.00,90.00,1290.00,'pendiente',18,5,NULL,19,NULL),(74,'2026-03-15 16:42:03',1000.00,100.00,150.00,1250.00,'pendiente',18,6,NULL,19,NULL),(75,'2026-03-15 16:42:12',3500.00,100.00,525.00,4125.00,'pendiente',18,4,NULL,19,NULL),(76,'2026-03-15 16:42:23',1000.00,100.00,150.00,1250.00,'pendiente',18,6,NULL,19,NULL),(77,'2026-03-15 16:42:46',1000.00,600.00,150.00,1750.00,'pendiente',18,5,NULL,19,NULL),(78,'2026-03-15 16:42:57',600.00,100.00,90.00,790.00,'confirmado',18,6,NULL,19,NULL),(79,'2026-03-15 16:51:37',600.00,100.00,90.00,790.00,'entregado',18,6,NULL,19,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto_imagenes`
--

/*!40000 ALTER TABLE `producto_imagenes` DISABLE KEYS */;
INSERT INTO `producto_imagenes` VALUES (20,9,'img/productos/prod_9_1774139472_0.png',4,'2026-03-22 00:31:12'),(21,8,'img/productos/prod_8_1774161593_0.png',1,'2026-03-22 06:39:53'),(22,7,'img/productos/prod_7_1774161603_0.png',1,'2026-03-22 06:40:03'),(23,6,'img/productos/prod_6_1774161617_0.png',1,'2026-03-22 06:40:17');
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
  `precio_costo` decimal(10,2) DEFAULT 0.00,
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (6,'FU-CA-0001','Nike Mercurial Vapor 15 Academy','Breve descricpion del proudcto',3500.00,120.00,100,'disponible','2026-03-02 06:42:50',17,17,NULL,0,NULL,NULL),(7,'FU-CA-0002','New Balance Fresh Foam Arishi','Breve descripcion del Producto',1000.00,200.00,100,'disponible','2026-03-02 07:22:32',17,17,NULL,0,NULL,NULL),(8,'GY-PE-0001','Mancuernas Ajustables 40 lb','set de mancuernas de diferentes pesos',600.00,100.00,100,'disponible','2026-03-02 23:04:20',19,17,NULL,0,NULL,NULL),(9,'TE-RA-0001','Raquetas profesionales','',150.00,0.00,98,'disponible','2026-03-02 23:06:11',21,17,NULL,0,NULL,NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','administrara dashboard y demas'),(2,'Vendedor','administrara parte del dashboard\r\n'),(3,'Clientes','cliente final');
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
INSERT INTO `tipos_cuenta_banco` VALUES (1,'Cuenta Ahorro Comun'),(2,'Cuenta en Dolares'),(3,'Cuenta de Cheques');
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Jonathan Orellana','jonathan.medez@uph.edu.hn','$2y$10$/tKq7r1HMZwjjd1zt9wwle5SN2eNSaMA9hBJowBEI6aFbnVinZIWe','activo','2026-03-21 23:22:20',1),(2,'Daniel Martinez','Daniel@gmail.com','$2y$10$/tKq7r1HMZwjjd1zt9wwle5SN2eNSaMA9hBJowBEI6aFbnVinZIWe','activo','2026-03-21 23:22:20',2),(3,'Cliente Prueba','cliente@empresa.com','$2y$10$/tKq7r1HMZwjjd1zt9wwle5SN2eNSaMA9hBJowBEI6aFbnVinZIWe','activo','2026-03-21 23:22:20',3),(4,'Admin Test','admin@empresa.com','$2y$10$/tKq7r1HMZwjjd1zt9wwle5SN2eNSaMA9hBJowBEI6aFbnVinZIWe','activo','2026-03-21 23:22:20',2),(5,'Vendedor Test','vendedor@empresa.com','$2y$10$/tKq7r1HMZwjjd1zt9wwle5SN2eNSaMA9hBJowBEI6aFbnVinZIWe','activo','2026-03-21 23:22:20',2),(6,'EDUARDO AVILA','ea3192571@gmail.com','$2y$12$Cdts8yhR/OaP3N8be6n9OetV9mwdZsscpIaCHoOab/AzMc1Ll1mgm','activo','2026-03-22 07:15:14',3),(7,'Eduardo Mauricio Ávila','ea31925712@gmail.com','$2y$10$uMYEBXmm4yIXUR68QY/mqe4kuQBu6p00PNwYDg3PPJLiUJ3TYYdli','activo','2026-03-22 07:16:09',1);
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

-- Dump completed on 2026-03-22  1:29:35
