-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 07, 2025 at 04:00 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homiem`
--

-- --------------------------------------------------------

--
-- Table structure for table `admincontact`
--

DROP TABLE IF EXISTS `admincontact`;
CREATE TABLE IF NOT EXISTS `admincontact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admincontact`
--

INSERT INTO `admincontact` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Arkam', 'arkam@gmail.com', 'Become a partner', 'I would like to join your community', '2025-03-29 03:34:12');

-- --------------------------------------------------------

--
-- Table structure for table `adminsign`
--

DROP TABLE IF EXISTS `adminsign`;
CREATE TABLE IF NOT EXISTS `adminsign` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `adminsign`
--

INSERT INTO `adminsign` (`id`, `email`, `password`) VALUES
(1, 'homiemart@gmail.com', '$2y$10$As5nROVGSqUyKaoV.06jSO3t9.MSr4gB0.cG8tLRsl2f3DcmOsYMC');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_email` varchar(100) NOT NULL,
  `product_id` int NOT NULL,
  `product_name` varchar(120) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `image` varchar(150) NOT NULL,
  `quantity` int NOT NULL,
  `created_at` timestamp NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_email`, `product_id`, `product_name`, `price`, `image`, `quantity`, `created_at`) VALUES
(6, 'santhosmd69@gmail.com', 5, 'Stone Garden Marker', 650, 'uploads/stone.jpg', 4, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
CREATE TABLE IF NOT EXISTS `chat_messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_email` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `homemaker_email` varchar(255) NOT NULL,
  `homemaker_name` varchar(255) NOT NULL,
  `message` text,
  `reply_message` text,
  `image` varchar(255) DEFAULT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `customer_email`, `customer_name`, `homemaker_email`, `homemaker_name`, `message`, `reply_message`, `image`, `product_id`, `created_at`) VALUES
(1, 'santhosmd69@gmail.com', 'Dharshan', 'arkam@gmail.com', 'Arkam', 'Pricee kooda bro', 'ok bro yenna price yedhir paakuriga?', 'uploads/67e7683b2eedc_rice.jpg', 46, '2025-03-29 03:25:47'),
(2, 'santhosmd69@gmail.com', 'Dharshan', 'sharuban@gmail.com', 'Sharuban', 'i need more like this products can you give us small discount', 'yes we can ', 'uploads/67e77ab7f1f13_necklace.jpg', 14, '2025-03-29 04:44:39'),
(3, 'santhosmd69@gmail.com', 'Dharshan', 'madhu@gmail.com', 'Madhu', 'This is too much price can you less this price?', 'Yeladhu poda mairu', 'uploads/67e78520bb84e_herbal soap.jpg', 47, '2025-03-29 05:29:04');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_email` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `total_price` decimal(10,0) NOT NULL,
  `payment_status` enum('Pending','Processing','Completed','Cancelled') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_email`, `first_name`, `last_name`, `address`, `city`, `state`, `postal_code`, `phone`, `total_price`, `payment_status`, `created_at`) VALUES
(1, 'santhosmd69@gmail.com', 'Arkam', 'arkam', 'Beruwala', 'Beruwala', 'Colombo', '12070', '0778002997', 0, 'Cancelled', '2025-03-29 03:29:23'),
(2, 'pradhiksha@gmail.com', 'Pradhiksha', 'Pradhiksha', 'No 59/A Gorthii Estate Maskeliya, Hatton', 'Hatton', 'Colombo', '2424', '0778597438', 0, 'Cancelled', '2025-03-29 03:41:35'),
(3, 'santhosmd69@gmail.com', 'Pradhiksha', 'Pradhiksha', 'No 59/A Gorthii Estate Maskeliya, Hatton', 'Hatton', 'Nuweraeliya', '2424', '0778597438', 0, 'Completed', '2025-03-29 04:48:23'),
(4, 'santhosmd69@gmail.com', 'Pradhiksha', 'Pradhiksha', 'No 59/A Gorthii Estate Maskeliya, Hatton', 'Hatton', 'Nuweraeliya', '2424', '0778597438', 0, 'Cancelled', '2025-03-29 05:30:42');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`item_id`, `order_id`, `product_name`, `quantity`, `price`, `total`, `created_at`) VALUES
(1, 1, 'Biriyani', 3, 1200.00, 3600.00, '2025-03-29 03:29:23'),
(2, 2, 'Butter Biscuits', 3, 350.00, 1050.00, '2025-03-29 03:41:35'),
(3, 3, 'Beaded Necklace', 4, 1200.00, 4800.00, '2025-03-29 04:48:23'),
(4, 4, 'soap', 2, 7000.00, 14000.00, '2025-03-29 05:30:42');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_image` varchar(255) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_description` text NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `homemaker_email` varchar(255) DEFAULT NULL,
  `homemaker_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_image`, `product_name`, `product_description`, `product_price`, `created_at`, `homemaker_email`, `homemaker_name`) VALUES
(1, 'uploads/shawl.jpg', 'Indigo Shawl', 'This handwoven shawl is dyed with natural indigo, giving it a beautiful, deep blue color. The soft fabric provides warmth and style, making it perfect for cool evenings. Each piece is unique, showcasing expert craftsmanship.', 1500.00, '2025-03-25 15:54:14', 'alice@gmail.com', 'Alice'),
(2, 'uploads/67e2d4feb6a43_Hanging.jpg', 'Batik Wall Hanging', 'A beautiful batik wall hanging, hand-dyed with intricate designs that tell stories of culture and tradition. Each piece is made with love and precision to add color and warmth to your home. Perfect for any living room or bedroom decor.', 2000.00, '2025-03-25 15:55:34', 'alice@gmail.com', 'Alice'),
(3, 'uploads/pots.png', 'Clay Planters', 'These handmade clay planters are perfect for adding a rustic touch to your garden. Each planter is carefully crafted with unique textures and earthy tones to complement any plant. Ideal for both indoor and outdoor use.', 1200.00, '2025-03-25 16:18:08', 'bob@gmail.com', 'Bob'),
(4, 'uploads/vase.jfif', 'Terracotta Vase', 'A beautiful handmade terracotta vase that adds an earthy charm to your garden. Each piece is carefully shaped and fired to perfection, providing a stylish home for your flowers and plants. Ideal for both indoors and outdoors.', 1500.00, '2025-03-25 16:18:41', 'bob@gmail.com', 'Bob'),
(5, 'uploads/stone.jpg', 'Stone Garden Marker', 'These handcrafted stone garden markers are perfect for identifying your plants in style. Each marker is uniquely carved and weather-resistant, making them ideal for outdoor gardens. A charming and functional addition to any garden.', 650.00, '2025-03-25 16:19:12', 'bob@gmail.com', 'Bob'),
(6, 'uploads/carrot-cake.jpg', 'Carrot Cake', 'A moist carrot cake, filled with grated carrots and lightly spiced with cinnamon. Topped with a smooth cream cheese frosting, this cake is both flavorful and wholesome. Perfect for any occasion.', 1000.00, '2025-03-25 16:28:13', 'charlie@gmail.com', 'Charlie'),
(7, 'uploads/Chocolate-Muffin.jpg', 'Choco Muffins', 'Decadent chocolate muffins, made with high-quality cocoa and topped with chocolate chips. These soft, spongy treats are a chocolate lover\'s dream, perfect for breakfast or dessert.', 400.00, '2025-03-25 16:33:43', 'charlie@gmail.com', 'Charlie'),
(8, 'uploads/67e2db9ab6edb_fruit cake.jpg', 'Fruit Cake', 'A moist, rich fruit cake packed with a variety of dried fruits and nuts. Infused with a hint of rum and perfectly spiced, making it an ideal festive treat. Great for celebrations and gifting.', 1200.00, '2025-03-25 16:34:30', 'charlie@gmail.com', 'Charlie'),
(9, 'uploads/butter_biscuits.jpg', 'Butter Biscuits', 'These buttery, melt-in-your-mouth biscuits are made with premium ingredients for a delicate flavor. Crispy on the outside, soft on the inside, perfect for pairing with tea or coffee.', 350.00, '2025-03-25 16:37:26', 'charlie@gmail.com', 'Charlie'),
(10, 'uploads/Cinnamon.jpg', 'Cinnamon Rolls', 'Soft, fluffy cinnamon rolls filled with a rich, aromatic cinnamon sugar blend. Baked to perfection with a sweet glaze on top. A delightful treat for breakfast or as a snack with coffee.', 500.00, '2025-03-25 16:37:52', 'charlie@gmail.com', 'Charlie'),
(11, 'uploads/Sculpture.jpg', 'Wooden Sculpture', 'A finely carved wooden sculpture, showcasing intricate detailing and craftsmanship. Each piece is handmade from high-quality wood, bringing nature into your home. Perfect for adding an artistic touch to any room or as a unique gift.', 3000.00, '2025-03-25 16:44:24', 'sharuban@gmail.com', 'Sharuban'),
(12, 'uploads/clay.jpg', 'Clay Pottery', 'Handcrafted clay pottery with a rustic charm, made using traditional techniques. The smooth finish and natural textures make it a beautiful addition to your home decor. Ideal for holding plants or as an elegant standalone piece.', 1500.00, '2025-03-25 16:45:16', 'sharuban@gmail.com', 'Sharuban'),
(13, 'uploads/basket.jpg', 'Jute Basket', 'A durable and eco-friendly jute basket, woven by hand with care and precision. Its natural texture and sturdy build make it perfect for storage or as a decorative item. Adds a rustic, earthy vibe to any room.', 750.00, '2025-03-25 16:45:42', 'sharuban@gmail.com', 'Sharuban'),
(14, 'uploads/necklace.jpg', 'Beaded Necklace', 'A handmade beaded necklace, designed with vibrant colors and patterns. Each bead is carefully selected to create a unique piece that adds elegance to your outfit. Perfect for gifting or as a personal accessory.', 1200.00, '2025-03-25 16:46:10', 'sharuban@gmail.com', 'Sharuban'),
(15, 'uploads/rotti.jpg', 'Coconut Roti', 'A soft and slightly crispy flatbread made with freshly grated coconut and wheat flour. Perfectly pairs with spicy sambal or curry for a traditional Sri Lankan meal. Made fresh to bring authentic flavors to your table.', 250.00, '2025-03-25 17:49:12', 'eva@gmail.com', 'Eva'),
(16, 'uploads/Spicy-Rice.jpg', 'Spice Rice', 'Fragrant basmati rice infused with local spices like cinnamon, cardamom, and cloves. Cooked to perfection, delivering a flavorful and aromatic dish. A delicious side for any curry or grilled meat.', 500.00, '2025-03-25 17:49:46', 'eva@gmail.com', 'Eva'),
(17, 'uploads/plum.jpg', 'Jaggery Cake', 'A moist and rich cake made with natural jaggery, coconut milk, and a hint of cinnamon. Brings a traditional touch to your tea-time with its deep caramel-like sweetness. Perfect for dessert lovers.', 600.00, '2025-03-25 17:50:47', 'eva@gmail.com', 'Eva'),
(18, 'uploads/banana-fritter.jpg', 'Banana Fritters', 'Crispy on the outside and soft on the inside, these golden banana fritters are deep-fried to perfection. Lightly coated with cinnamon and honey for a delightful sweetness. A perfect snack for any time of the day.', 350.00, '2025-03-25 17:51:20', 'eva@gmail.com', 'Eva'),
(19, 'uploads/kola-kedha.jfif', 'Herbal Porridge', 'A nutritious and soothing porridge made with local herbal leaves, coconut milk, and rice. Packed with health benefits, offering a delicious and refreshing start to your day. Traditionally enjoyed warm with a pinch of salt.', 400.00, '2025-03-25 17:52:17', 'eva@gmail.com', 'Eva'),
(20, 'uploads/bowl.jpg', 'Coconut Bowl', 'Crafted from discarded coconut shells, this eco-friendly bowl is perfect for serving meals or snacks. Each piece is polished for a smooth finish while retaining its natural charm. A great alternative to plastic kitchenware.', 950.00, '2025-03-26 04:16:53', 'david@gmail.com', 'David'),
(21, 'uploads/bamboo brush.jpg', 'Bamboo Toothbrush', 'Made from biodegradable bamboo, this toothbrush is a sustainable alternative to plastic ones. Its soft bristles ensure gentle cleaning while reducing environmental impact. A perfect step toward an eco-friendly lifestyle.', 450.00, '2025-03-26 04:17:22', 'david@gmail.com', 'David'),
(22, 'uploads/jutebag.jpg', 'Jute Tote', 'This handmade jute tote bag is durable, stylish, and completely biodegradable. Ideal for shopping, carrying essentials, or everyday use without harming the planet. Say goodbye to plastic bags with this sustainable choice.', 1200.00, '2025-03-26 04:17:50', 'david@gmail.com', 'David'),
(23, 'uploads/beeswaxwarp.jpg', 'Beeswax Wrap', 'A reusable food wrap made with organic cotton and beeswax, replacing plastic cling film. Keeps food fresh while being washable and compostable after use. A natural and sustainable way to store leftovers.', 750.00, '2025-03-26 04:18:15', 'david@gmail.com', 'David'),
(24, 'uploads/coconut candle.jfif', 'Coconut Candle', 'A handcrafted soy wax candle set in a natural coconut shell for a rustic, eco-friendly touch. Provides a long-lasting, soothing fragrance while being fully biodegradable. Perfect for home décor and relaxation.', 1500.00, '2025-03-26 04:18:50', 'david@gmail.com', 'David'),
(25, 'uploads/floral-canvas.jpg', 'Floral Canvas', 'A delicate hand-painted canvas featuring vibrant floral patterns that bring nature indoors. Each brushstroke captures the beauty of blossoms in a unique artistic style. Perfect for adding warmth and elegance to any space.', 2500.00, '2025-03-27 13:21:40', 'fay@gmail.com', 'Fay'),
(26, 'uploads/67e5522f42cb8_waves.jpg', 'Abstract Waves', 'A mesmerizing abstract painting inspired by ocean waves, blending blue and white hues. The textured strokes create a calming effect, making it an ideal centerpiece. Handcrafted with high-quality acrylic on canvas.', 3200.00, '2025-03-27 13:22:09', 'fay@gmail.com', 'Fay'),
(27, 'uploads/67e552236650d_golden_sunrise.jpg', 'Golden Sunrise', 'A stunning sunrise painting with golden hues reflecting on a peaceful landscape. The handmade artwork captures the beauty of dawn, filling any room with serenity. Created using mixed media for a rich, textured finish.', 4000.00, '2025-03-27 13:22:44', 'fay@gmail.com', 'Fay'),
(28, 'uploads/tribal.jpg', 'Tribal Portrait', 'A detailed hand-drawn portrait inspired by indigenous tribal art, showcasing deep cultural heritage. The fine details and earthy tones make it a unique statement piece. Crafted on premium handmade paper using eco-friendly dyes.', 3500.00, '2025-03-27 13:26:49', 'fay@gmail.com', 'Fay'),
(29, 'uploads/vintage.jpg', 'Vintage Scenery', 'A beautifully detailed countryside landscape painting with a vintage aesthetic. The soft color palette and intricate details transport viewers to a peaceful, nostalgic world. Perfect for classic and rustic home decor.', 2800.00, '2025-03-27 13:27:47', 'fay@gmail.com', 'Fay'),
(30, 'uploads/Blanket.jpeg', 'Cozy Blanket', 'A warm and soft hand-knitted blanket made from premium yarn, perfect for chilly nights. The intricate stitching adds elegance and texture to any space. Ideal for gifting or personal use.', 3500.00, '2025-03-27 13:40:06', 'george@gmail.com', 'George'),
(31, 'uploads/coaster.jpg', 'Lace Coaster', 'Delicate and beautifully crocheted, this coaster adds charm to your table. Made from fine cotton thread, it protects surfaces while enhancing decor. A perfect blend of functionality and elegance.', 600.00, '2025-03-27 13:40:33', 'george@gmail.com', 'George'),
(32, 'uploads/67e555b99b4e5_scarf.jfif', 'Knitted Scarf', 'A stylish and cozy scarf hand-knitted with love, featuring a soft and breathable texture. The timeless design pairs well with any outfit, making it a must-have accessory. Available in various colors.', 1800.00, '2025-03-27 13:41:09', 'george@gmail.com', 'George'),
(33, 'uploads/bag.jpg', 'Crochet Bag', 'A trendy and eco-friendly crochet bag, perfect for casual outings or shopping. Made with sturdy yarn, it combines style and durability effortlessly. Handcrafted with attention to detail.', 2200.00, '2025-03-27 13:41:33', 'george@gmail.com', 'George'),
(34, 'uploads/beanie.jpg', 'Floral Beanie', 'This adorable crochet beanie features a beautiful floral pattern, adding a touch of elegance. Made from soft wool, it keeps you warm while looking fashionable. A great addition to your winter collection.', 1500.00, '2025-03-27 13:42:03', 'george@gmail.com', 'George'),
(36, 'uploads/herbal soap.jpg', 'Herbal Soap', 'Crafted with natural herbs like neem, turmeric, and aloe vera, this soap gently cleanses and nourishes the skin. Free from harsh chemicals, it helps maintain a healthy glow. Ideal for sensitive and all skin types.', 750.00, '2025-03-27 13:56:13', 'helen@gmail.com', 'Helen'),
(37, 'uploads/candle.jpg', 'Aroma Candle', 'Handmade with soy wax and infused with essential oils, this candle creates a calming atmosphere. Perfect for relaxation, meditation, or stress relief. Comes in soothing scents like lavender, sandalwood, and rose.', 1200.00, '2025-03-27 13:56:36', 'helen@gmail.com', 'Helen'),
(38, 'uploads/salts.png', 'Bath Salts', 'A blend of Epsom salt, Himalayan pink salt, and dried herbs for a rejuvenating bath experience. Helps soothe sore muscles, detoxify the skin, and promote relaxation. Infused with essential oils for added aromatherapy benefits.', 1500.00, '2025-03-27 13:57:18', 'helen@gmail.com', 'Helen'),
(39, 'uploads/lip balm.jpg', 'Lip Balm', 'Made with beeswax, coconut oil, and natural fruit extracts, this balm keeps lips soft and hydrated. Free from artificial fragrances, making it safe for daily use. Available in flavors like vanilla, mint, and strawberry.', 550.00, '2025-03-27 13:57:54', 'helen@gmail.com', 'Helen'),
(40, 'uploads/notebook.jpg', 'Floral Notebook', 'A beautifully handcrafted notebook with floral-patterned covers made from recycled paper. Ideal for journaling, sketching, or daily notes, adding an artistic touch to your writing. Eco-friendly and uniquely designed for creative minds.', 950.00, '2025-03-27 14:09:01', 'ivy@gmail.com', 'Ivy'),
(41, 'uploads/journal.jpg', 'Vintage Journal', 'A rustic-style journal bound with handmade paper, featuring a textured cover for a timeless feel. Perfect for capturing thoughts, sketches, or personal memories in a unique way. Lightweight and easy to carry anywhere.', 1200.00, '2025-03-27 14:09:27', 'ivy@gmail.com', 'Ivy'),
(42, 'uploads/card.jfif', 'Origami Cards', 'Hand-folded greeting cards with intricate origami designs, adding a creative touch to your messages. Each card is unique, made with high-quality paper and colorful patterns. Ideal for special occasions or heartfelt notes.', 500.00, '2025-03-27 14:09:48', 'ivy@gmail.com', 'Ivy'),
(43, 'uploads/envelope.jpg', 'Handmade Envelope', 'A set of artistic envelopes crafted from eco-friendly handmade paper, perfect for letters or gifts. Each envelope is decorated with delicate designs, adding elegance to your messages. A sustainable and stylish alternative to regular envelopes. (per set of 5)', 400.00, '2025-03-27 14:10:31', 'ivy@gmail.com', 'Ivy'),
(44, 'uploads/greeting cards.jpeg', 'Calligraphy Cards', 'Elegant hand-lettered cards featuring beautiful calligraphy on textured paper, perfect for special occasions. Each card is crafted with care to add a personal and artistic touch. Available in different themes for birthdays, anniversaries, and more.', 600.00, '2025-03-27 14:10:58', 'ivy@gmail.com', 'Ivy'),
(45, 'uploads/paper-boxes.jpg', 'Paper Giftbox', 'A charming handmade gift box crafted from recycled paper, decorated with intricate patterns. Perfect for wrapping small presents, jewelry, or keepsakes in an eco-friendly way. Lightweight yet sturdy, making it both practical and stylish.', 750.00, '2025-03-27 14:11:40', 'ivy@gmail.com', 'Ivy'),
(46, 'uploads/rice.jpg', 'Biriyani', 'This Restaurant-Style Mexican Rice is the perfect side dish for any Mexican meal. The rice has the rich flavor and slightly dry texture as the rice served in most Mexican restaurants.', 1200.00, '2025-03-29 03:25:12', 'arkam@gmail.com', 'Arkam'),
(47, 'uploads/herbal soap.jpg', 'soap', 'ehuwehuUEHEJNJendjgirjghwrhgu9rhguhrwhghrhghrgedgfjeuhg0r', 7000.00, '2025-03-29 05:28:01', 'madhu@gmail.com', 'Madhu');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

DROP TABLE IF EXISTS `register`;
CREATE TABLE IF NOT EXISTS `register` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` enum('Customer','Homemaker') NOT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `fname`, `email`, `mobile`, `password`, `role`, `business_type`) VALUES
(1, 'Dharshan', 'santhosmd69@gmail.com', '0778597438', '$2y$10$dzNDYf7X5rHTWqJrsrsrA.yJxzKXFBa.nzdPxPvxJzD43h/6yDz9C', 'Customer', NULL),
(2, 'Sharuban', 'sharuban@gmail.com', '0765737857', '$2y$10$5iUEViV.VKHDpQc4tF5fruPxav4QOe6jogJryo/2rWV9o4MrS.e22', 'Homemaker', 'Handicrafts'),
(3, 'Alice', 'alice@gmail.com', '0771234567', '$2y$10$r1.Fl7JFMy2ttqH4qyNOq.zc/ikuVVH71HD3Qt9/Oksrg7Eqns8RO', 'Homemaker', 'Dye Crafts'),
(4, 'Bob', 'bob@gmail.com', '0782345678', '$2y$10$TianqeSKSc6/UzmM/ayWeejVkhXDyTGoKy4lqYtQvnzK0/kH4s/Ee', 'Homemaker', 'Gardening Products'),
(5, 'Charlie', 'charlie@gmail.com', '0793456789', '$2y$10$I2geAx9MDmL2VX0AlvIav.qu2qwo3XmN/Ri9OlhD0Go9t8c3Jf/Im', 'Homemaker', 'Baking'),
(6, 'David', 'david@gmail.com', '0704567890', '$2y$10$JWVaRq51.UG1EEqAolu78OK9F8m8bp7YT2AGleBLWUtw4PPftaE2W', 'Homemaker', 'Sustainable Products'),
(7, 'Eva', 'eva@gmail.com', '0715678901', '$2y$10$EE1DckGOmnVfnz3v.QDlGelWYAZLkvqLcq5.U2kXEXe2n/N6nnbPa', 'Homemaker', 'Cooking'),
(8, 'Fay', 'fay@gmail.com', '0726789012', '$2y$10$oLWu4wORHt9ca/XLhfHkwuwZhGoUCw/itXWSFDZ6IXWdEIAKv4arW', 'Homemaker', 'Art and Painting'),
(9, 'George', 'george@gmail.com', '0737890123', '$2y$10$BFnd8C6NDTxDRvNEH94oq.RinBVn0FH0.bH/UiQKPC.ygSeY3Bqnm', 'Homemaker', 'Knitting and Crochet'),
(10, 'Helen', 'helen@gmail.com', '0748901234', '$2y$10$9smyZD.wpHqlGY6WgsXAYuE0ESUON2GE3aEyIa1waoSgsZM9tPdsG', 'Homemaker', 'Wellness Products'),
(11, 'Ivy', 'ivy@gmail.com', '0759012345', '$2y$10$L9zoCUh1Gc3E6haiqT939.fiO/fIwtA2vxkhH2wcMWYhRjbVgWB7O', 'Homemaker', 'Stationery and Paper Crafts'),
(12, 'Pradhiksha', 'pradhiksha@gmail.com', '074 056 3024', '$2y$10$U/CBGxiUOA.lzHT7CHguIO8gIAnI9JW.B1EcEpQHeSilKUwLZEo.O', 'Customer', NULL),
(13, 'Arkam', 'arkam@gmail.com', '0778002997', '$2y$10$QfBxQLbnrjDyW.8SEemsLuxCJrLlhiMQtxxSll1FhYlg5mCFF/9za', 'Homemaker', 'Cooking'),
(14, 'Madhu', 'madhu@gmail.com', '0781402835', '$2y$10$0KY.cOn4w42RQNjYJ36jIeg5AUyrmzvHHrWETuvUt53b8eik9Ds9O', 'Homemaker', 'Wellness Products');

-- --------------------------------------------------------

--
-- Table structure for table `revenue`
--

DROP TABLE IF EXISTS `revenue`;
CREATE TABLE IF NOT EXISTS `revenue` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` varchar(100) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `admin_revenue` decimal(10,2) DEFAULT NULL,
  `homemaker_revenue` decimal(10,2) DEFAULT NULL,
  `homemaker_email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `revenue`
--

INSERT INTO `revenue` (`id`, `order_id`, `product_name`, `admin_revenue`, `homemaker_revenue`, `homemaker_email`) VALUES
(1, '1', 'Biriyani', 1080.00, 2520.00, 'arkam@gmail.com'),
(2, '2', 'Butter Biscuits', 315.00, 735.00, 'charlie@gmail.com'),
(3, '3', 'Beaded Necklace', 1440.00, 3360.00, 'sharuban@gmail.com'),
(4, '4', 'soap', 4200.00, 9800.00, 'madhu@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `visiters`
--

DROP TABLE IF EXISTS `visiters`;
CREATE TABLE IF NOT EXISTS `visiters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Mobile` varchar(20) NOT NULL,
  `Address` text NOT NULL,
  `Business` varchar(255) NOT NULL,
  `Skills_Products` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `visiters`
--

INSERT INTO `visiters` (`id`, `Username`, `Email`, `Mobile`, `Address`, `Business`, `Skills_Products`, `created_at`) VALUES
(1, 'Arkam', 'arkam@gmail.com', '0778002997', 'Beruwala', 'Cooking', 'I have done homemade foods and did coconut  sambal', '2025-03-29 03:18:52'),
(2, 'Mohana Dharshan', 'santhosmd69@gmail.com', '0778597438', 'No 59/A Gorthii Estate Maskeliya, Hatton', 'Handicrafts', 'i have done more handicrafts', '2025-03-29 04:42:35'),
(3, 'madhu', 'madhu@gmail.com', '0781402835', 'colobmo', 'Wellness Products', 'I have done more wellness products', '2025-03-29 05:25:29');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
