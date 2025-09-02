-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 02, 2025 at 07:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `disaster_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `disaster`
--

CREATE TABLE `disaster` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `disaster`
--

INSERT INTO `disaster` (`id`, `name`, `type`, `location`) VALUES
(8, 'Southern Flood', 'Flood', 'Noakhali');

-- --------------------------------------------------------

--
-- Table structure for table `relief_camp`
--

CREATE TABLE `relief_camp` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` int(11) DEFAULT NULL,
  `disaster_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relief_camp`
--

INSERT INTO `relief_camp` (`id`, `name`, `capacity`, `disaster_id`) VALUES
(4, 'Noakhali Camp', 2000, 8);

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE `resource` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`id`, `name`, `quantity`, `unit`) VALUES
(1, 'Rice', 20, 'kg');

-- --------------------------------------------------------

--
-- Table structure for table `victim`
--

CREATE TABLE `victim` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `disaster_id` int(11) DEFAULT NULL,
  `camp_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `victim`
--

INSERT INTO `victim` (`id`, `name`, `disaster_id`, `camp_id`) VALUES
(1, 'Tayeb', 8, 4);

-- --------------------------------------------------------

--
-- Table structure for table `volunteer`
--

CREATE TABLE `volunteer` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `skill` varchar(100) DEFAULT NULL,
  `camp_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteer`
--

INSERT INTO `volunteer` (`id`, `name`, `skill`, `camp_id`) VALUES
(1, 'Nafi', 'Medical', 4),
(2, 'Batman', 'Medical', 4);

--
-- Triggers `volunteer`
--
DELIMITER $$
CREATE TRIGGER `after_volunteer_update` AFTER UPDATE ON `volunteer` FOR EACH ROW BEGIN
    IF NEW.camp_id != OLD.camp_id OR 
       (NEW.camp_id IS NULL AND OLD.camp_id IS NOT NULL) OR
       (NEW.camp_id IS NOT NULL AND OLD.camp_id IS NULL) THEN
        INSERT INTO volunteer_assignment_log (
            volunteer_id,
            volunteer_name,
            old_camp_id,
            new_camp_id,
            change_date
        ) VALUES (
            NEW.id,
            NEW.name,
            OLD.camp_id,
            NEW.camp_id,
            NOW()
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_assignment_log`
--

CREATE TABLE `volunteer_assignment_log` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) DEFAULT NULL,
  `volunteer_name` varchar(100) DEFAULT NULL,
  `old_camp_id` int(11) DEFAULT NULL,
  `new_camp_id` int(11) DEFAULT NULL,
  `change_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `disaster`
--
ALTER TABLE `disaster`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `relief_camp`
--
ALTER TABLE `relief_camp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disaster_id` (`disaster_id`);

--
-- Indexes for table `resource`
--
ALTER TABLE `resource`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `victim`
--
ALTER TABLE `victim`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disaster_id` (`disaster_id`),
  ADD KEY `camp_id` (`camp_id`);

--
-- Indexes for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `camp_id` (`camp_id`);

--
-- Indexes for table `volunteer_assignment_log`
--
ALTER TABLE `volunteer_assignment_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `disaster`
--
ALTER TABLE `disaster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `relief_camp`
--
ALTER TABLE `relief_camp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `resource`
--
ALTER TABLE `resource`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `victim`
--
ALTER TABLE `victim`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `volunteer`
--
ALTER TABLE `volunteer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `volunteer_assignment_log`
--
ALTER TABLE `volunteer_assignment_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `relief_camp`
--
ALTER TABLE `relief_camp`
  ADD CONSTRAINT `relief_camp_ibfk_1` FOREIGN KEY (`disaster_id`) REFERENCES `disaster` (`id`);

--
-- Constraints for table `victim`
--
ALTER TABLE `victim`
  ADD CONSTRAINT `victim_ibfk_1` FOREIGN KEY (`disaster_id`) REFERENCES `disaster` (`id`),
  ADD CONSTRAINT `victim_ibfk_2` FOREIGN KEY (`camp_id`) REFERENCES `relief_camp` (`id`);

--
-- Constraints for table `volunteer`
--
ALTER TABLE `volunteer`
  ADD CONSTRAINT `volunteer_ibfk_1` FOREIGN KEY (`camp_id`) REFERENCES `relief_camp` (`id`);

--
-- Constraints for table `volunteer_assignment_log`
--
ALTER TABLE `volunteer_assignment_log`
  ADD CONSTRAINT `volunteer_assignment_log_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteer` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
