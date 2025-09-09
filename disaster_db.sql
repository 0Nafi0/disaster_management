-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 09:28 PM
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
-- Table structure for table `camp_resource`
--

CREATE TABLE `camp_resource` (
  `id` int(11) NOT NULL,
  `camp_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `camp_resource`
--

INSERT INTO `camp_resource` (`id`, `camp_id`, `resource_id`, `quantity`) VALUES
(1, 4, 1, 10);

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
-- Table structure for table `needs`
--

CREATE TABLE `needs` (
  `id` int(11) NOT NULL,
  `victim_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `fulfilled_quantity` int(11) DEFAULT 0,
  `status` enum('pending','partially_fulfilled','fulfilled') DEFAULT 'pending',
  `request_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `needs`
--

INSERT INTO `needs` (`id`, `victim_id`, `resource_id`, `quantity`, `fulfilled_quantity`, `status`, `request_date`) VALUES
(1, 1, 1, 5, 3, 'partially_fulfilled', '2025-09-09 15:10:26');

-- --------------------------------------------------------

--
-- Table structure for table `records`
--

CREATE TABLE `records` (
  `id` int(11) NOT NULL,
  `camp_id` int(11) NOT NULL,
  `disaster_id` int(11) NOT NULL,
  `available_resources` text DEFAULT NULL,
  `assignment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `records`
--

INSERT INTO `records` (`id`, `camp_id`, `disaster_id`, `available_resources`, `assignment_date`) VALUES
(1, 4, 8, 'Napa medicine - 200pc\r\nSaline - 20000pc', '2025-09-09 15:28:45');

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
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `type` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `disaster_id` int(11) NOT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `reports_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(1, 'Rice', 7, 'kg');

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
(2, 'Batman', 'Medical', 4),
(3, 'Tayeb', 'nine', 4);

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
-- Table structure for table `volunteering`
--

CREATE TABLE `volunteering` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `camp_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `volunteering`
--

INSERT INTO `volunteering` (`id`, `volunteer_id`, `camp_id`, `start_date`, `end_date`) VALUES
(1, 3, 4, '2025-09-12', '2025-09-26');

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

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_victim_help`
--

CREATE TABLE `volunteer_victim_help` (
  `id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `victim_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `help_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `camp_resource`
--
ALTER TABLE `camp_resource`
  ADD PRIMARY KEY (`id`),
  ADD KEY `camp_id` (`camp_id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Indexes for table `disaster`
--
ALTER TABLE `disaster`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `needs`
--
ALTER TABLE `needs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `victim_id` (`victim_id`),
  ADD KEY `resource_id` (`resource_id`);

--
-- Indexes for table `records`
--
ALTER TABLE `records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_record` (`camp_id`,`disaster_id`),
  ADD KEY `disaster_id` (`disaster_id`);

--
-- Indexes for table `relief_camp`
--
ALTER TABLE `relief_camp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disaster_id` (`disaster_id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `disaster_id` (`disaster_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

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
-- Indexes for table `volunteering`
--
ALTER TABLE `volunteering`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`),
  ADD KEY `camp_id` (`camp_id`);

--
-- Indexes for table `volunteer_assignment_log`
--
ALTER TABLE `volunteer_assignment_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`);

--
-- Indexes for table `volunteer_victim_help`
--
ALTER TABLE `volunteer_victim_help`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_id` (`volunteer_id`),
  ADD KEY `victim_id` (`victim_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `camp_resource`
--
ALTER TABLE `camp_resource`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `disaster`
--
ALTER TABLE `disaster`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `needs`
--
ALTER TABLE `needs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `records`
--
ALTER TABLE `records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `relief_camp`
--
ALTER TABLE `relief_camp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `volunteering`
--
ALTER TABLE `volunteering`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `volunteer_assignment_log`
--
ALTER TABLE `volunteer_assignment_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteer_victim_help`
--
ALTER TABLE `volunteer_victim_help`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `camp_resource`
--
ALTER TABLE `camp_resource`
  ADD CONSTRAINT `camp_resource_ibfk_1` FOREIGN KEY (`camp_id`) REFERENCES `relief_camp` (`id`),
  ADD CONSTRAINT `camp_resource_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`);

--
-- Constraints for table `needs`
--
ALTER TABLE `needs`
  ADD CONSTRAINT `needs_ibfk_1` FOREIGN KEY (`victim_id`) REFERENCES `victim` (`id`),
  ADD CONSTRAINT `needs_ibfk_2` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`);

--
-- Constraints for table `records`
--
ALTER TABLE `records`
  ADD CONSTRAINT `records_ibfk_1` FOREIGN KEY (`camp_id`) REFERENCES `relief_camp` (`id`),
  ADD CONSTRAINT `records_ibfk_2` FOREIGN KEY (`disaster_id`) REFERENCES `disaster` (`id`);

--
-- Constraints for table `relief_camp`
--
ALTER TABLE `relief_camp`
  ADD CONSTRAINT `relief_camp_ibfk_1` FOREIGN KEY (`disaster_id`) REFERENCES `disaster` (`id`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`disaster_id`) REFERENCES `disaster` (`id`);

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `report` (`id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteer` (`id`);

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
-- Constraints for table `volunteering`
--
ALTER TABLE `volunteering`
  ADD CONSTRAINT `volunteering_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteer` (`id`),
  ADD CONSTRAINT `volunteering_ibfk_2` FOREIGN KEY (`camp_id`) REFERENCES `relief_camp` (`id`);

--
-- Constraints for table `volunteer_assignment_log`
--
ALTER TABLE `volunteer_assignment_log`
  ADD CONSTRAINT `volunteer_assignment_log_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteer` (`id`);

--
-- Constraints for table `volunteer_victim_help`
--
ALTER TABLE `volunteer_victim_help`
  ADD CONSTRAINT `volunteer_victim_help_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `volunteer` (`id`),
  ADD CONSTRAINT `volunteer_victim_help_ibfk_2` FOREIGN KEY (`victim_id`) REFERENCES `victim` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
