-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 06:55 AM
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
-- Database: `userman`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `password`, `created_at`) VALUES
(3, 'nextfleetdynamics@gmail.com', '$2y$10$O93j.vmMDtti4dT2/4fudeM9BzQlf9pjzAM1u8VbI5kOnSYtu3WcC', '2025-02-02 04:29:01'),
(4, 'nextfleetdynamics@admin.com', '$2y$10$Bnf4/ys2rVgqYZl3cihh7.O9ykMQtQefjzCi3Peg0YSJ/DQqECc2S', '2025-02-03 09:11:00'),
(5, 'nextfleetdynamicsadmin@example.ph', '$2y$10$XtbfghRbXi2B40a/Io7ZqOLFwoIQIhkK2N.Txy73tF2lp72ArAKWy', '2025-02-19 04:44:33'),
(6, 'nextfleetdynamicsadmin@com.ph', '$2y$10$/dJ19dtjRBiGU/6qyR2Z5etSBIMRFRLk4qyf7ynTzg7pnahEzdtgq', '2025-02-19 08:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent','leave') NOT NULL,
  `daily_salary` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `employee_id`, `attendance_date`, `status`, `daily_salary`, `created_at`) VALUES
(1, 1, '2025-04-01', 'present', 800.00, '2025-04-03 10:48:05'),
(2, 1, '2025-04-02', 'present', 800.00, '2025-04-03 10:48:05'),
(3, 1, '2025-04-03', 'present', 800.00, '2025-04-03 10:48:05'),
(4, 1, '2025-04-04', 'present', 800.00, '2025-04-03 10:48:05'),
(5, 1, '2025-04-07', 'present', 800.00, '2025-04-03 10:48:05'),
(6, 1, '2025-04-08', 'present', 800.00, '2025-04-03 10:48:05'),
(7, 1, '2025-04-09', 'present', 800.00, '2025-04-03 10:48:05'),
(8, 1, '2025-04-10', 'absent', 0.00, '2025-04-03 10:48:05'),
(9, 1, '2025-04-11', 'present', 800.00, '2025-04-03 10:48:05'),
(10, 1, '2025-04-14', 'present', 800.00, '2025-04-03 10:48:05'),
(11, 1, '2025-04-15', 'present', 800.00, '2025-04-03 10:48:05'),
(12, 1, '2025-04-16', 'present', 800.00, '2025-04-03 10:48:05'),
(13, 1, '2025-04-17', 'leave', 800.00, '2025-04-03 10:48:05'),
(14, 1, '2025-04-18', 'present', 800.00, '2025-04-03 10:48:05'),
(15, 1, '2025-04-21', 'present', 800.00, '2025-04-03 10:48:05'),
(16, 1, '2025-04-22', 'present', 800.00, '2025-04-03 10:48:05'),
(17, 1, '2025-04-23', 'absent', 0.00, '2025-04-03 10:48:05'),
(18, 1, '2025-04-24', 'present', 800.00, '2025-04-03 10:48:05'),
(19, 1, '2025-04-25', 'present', 800.00, '2025-04-03 10:48:05'),
(20, 1, '2025-04-28', 'present', 800.00, '2025-04-03 10:48:05'),
(21, 1, '2025-04-29', 'present', 800.00, '2025-04-03 10:48:05'),
(22, 1, '2025-04-30', 'present', 800.00, '2025-04-03 10:48:05'),
(23, 1, '2025-04-12', '', 800.00, '2025-04-03 12:12:36'),
(24, 4, '2025-04-01', 'present', 800.00, '2025-04-04 09:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `compensation`
--

CREATE TABLE `compensation` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED DEFAULT NULL,
  `basic_salary` decimal(15,2) DEFAULT NULL,
  `allowances` decimal(15,2) DEFAULT NULL,
  `bonus` decimal(15,2) DEFAULT NULL,
  `deductions` decimal(15,2) DEFAULT NULL,
  `gross_salary` decimal(15,2) DEFAULT NULL,
  `net_salary` decimal(15,2) DEFAULT NULL,
  `compensation_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `age` int(11) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `position` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `date_hired` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `first_name`, `middle_name`, `last_name`, `email`, `contact`, `address`, `age`, `dob`, `gender`, `position`, `department`, `date_hired`, `created_at`) VALUES
(1, 'JOSHUA', 'M', 'IFURUNG', 'joshuhcute@gmail.com', '09238472323', 'blk6 lot 10 northville 1B punturin valenzuela city', 23, '2002-06-03', 'male', 'IT', 'IT Department', '2025-06-03', '2025-04-03 10:25:57'),
(2, 'Maria', 'L', 'Santos', 'maria.santos@example.com', '09171234567', '123 Main St, City A', 30, '1995-04-15', 'female', 'HR Manager', 'Human Resources', '2022-01-10', '2025-04-03 10:29:53'),
(3, 'John', 'K', 'Doe', 'john.doe@example.com', '09181234567', '456 Elm St, City B', 28, '1997-08-22', 'male', 'Sales Executive', 'Sales', '2023-03-12', '2025-04-03 10:29:53'),
(4, 'Anna', 'R', 'Smith', 'anna.smith@example.com', '09201234567', '789 Oak St, City C', 35, '1988-11-30', 'female', 'Marketing Director', 'Marketing', '2020-07-05', '2025-04-03 10:29:53'),
(5, 'Peter', 'J', 'Lee', 'peter.lee@example.com', '09211234567', '101 Pine St, City D', 40, '1983-02-10', 'male', 'Finance Analyst', 'Finance', '2018-09-01', '2025-04-03 10:29:53'),
(6, 'Linda', 'M', 'Garcia', 'linda.garcia@example.com', '09221234567', '202 Maple St, City E', 27, '1998-12-01', 'female', 'IT Support', 'IT Department', '2024-04-15', '2025-04-03 10:29:53'),
(7, 'Carlos', 'E', 'Ramirez', 'carlos.ramirez@example.com', '09231234567', '303 Birch St, City F', 32, '1991-06-25', 'male', 'HR Specialist', 'Human Resources', '2021-05-20', '2025-04-03 10:29:53'),
(8, 'Sofia', 'P', 'Martinez', 'sofia.martinez@example.com', '09241234567', '404 Cedar St, City G', 29, '1994-09-10', 'female', 'Sales Manager', 'Sales', '2022-11-30', '2025-04-03 10:29:53'),
(9, 'Robert', 'A', 'Brown', 'robert.brown@example.com', '09251234567', '505 Walnut St, City H', 45, '1978-03-05', 'male', 'Marketing Manager', 'Marketing', '2019-12-12', '2025-04-03 10:29:53'),
(10, 'Emily', 'J', 'Davis', 'emily.davis@example.com', '09261234567', '606 Cherry St, City I', 31, '1992-07-19', 'female', 'Finance Manager', 'Finance', '2020-06-25', '2025-04-03 10:29:53');

-- --------------------------------------------------------

--
-- Table structure for table `employee_benefits`
--

CREATE TABLE `employee_benefits` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` int(10) UNSIGNED DEFAULT NULL,
  `benefit_type` varchar(50) DEFAULT NULL,
  `benefit_amount` decimal(10,2) DEFAULT NULL,
  `contribution_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_benefits`
--

INSERT INTO `employee_benefits` (`id`, `employee_id`, `benefit_type`, `benefit_amount`, `contribution_type`) VALUES
(4, 1, 'SSS', 1000.00, 'Employee Contribution'),
(5, 1, 'Pag-IBIG', 500.00, 'Employee Contribution'),
(6, 2, 'PhilHealth', 300.00, 'Employer Contribution'),
(7, 1, 'SSS', 1000.00, 'Employee Contribution'),
(8, 1, 'Pag-IBIG', 500.00, 'Employee Contribution'),
(9, 2, 'PhilHealth', 300.00, 'Employer Contribution');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `id` int(30) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `salary` float NOT NULL DEFAULT 0,
  `present` float NOT NULL DEFAULT 0,
  `late_undertime` float NOT NULL DEFAULT 0,
  `witholding_tax` float NOT NULL DEFAULT 0,
  `net` float NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

CREATE TABLE `payslips` (
  `id` int(11) NOT NULL,
  `employee_name` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `basic_rate` decimal(10,2) NOT NULL,
  `overtime` decimal(10,2) NOT NULL,
  `allowance` decimal(10,2) NOT NULL,
  `pagibig` decimal(10,2) NOT NULL,
  `sss` decimal(10,2) NOT NULL,
  `philhealth` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `total_earning` decimal(10,2) NOT NULL,
  `total_deduction` decimal(10,2) NOT NULL,
  `net_pay` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payslips`
--

INSERT INTO `payslips` (`id`, `employee_name`, `department`, `designation`, `basic_rate`, `overtime`, `allowance`, `pagibig`, `sss`, `philhealth`, `tax`, `total_earning`, `total_deduction`, `net_pay`, `created_at`) VALUES
(1, '', '', '', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, '2025-04-04 21:59:45');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'employee');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `compensation`
--
ALTER TABLE `compensation`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `compensation`
--
ALTER TABLE `compensation`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `id` int(30) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
