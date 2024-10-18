CREATE TABLE Product (
  ProductID INT PRIMARY KEY,
  Description VARCHAR(255),
  Price DECIMAL(10, 2),
  Type VARCHAR(10), -- 'delivery' or 'collection'
  Dimensions VARCHAR(50), -- Only for delivery products
  Weight DECIMAL(10, 2) -- Only for collection products
);

CREATE TABLE Store (
  StoreID INT PRIMARY KEY,
  Location VARCHAR(255)
);

CREATE TABLE Employee (
  EmployeeID INT PRIMARY KEY,
  Name VARCHAR(255),
  Role VARCHAR(10), -- 'delivery' or 'collection'
  StoreID INT,
  FOREIGN KEY (StoreID) REFERENCES Store(StoreID)
);

CREATE TABLE Customer (
  CustomerID INT PRIMARY KEY,
  Name VARCHAR(255),
  Email VARCHAR(255)
);

CREATE TABLE Supplier (
  SupplierID INT PRIMARY KEY,
  Name VARCHAR(255),
  ContactInfo VARCHAR(255)
);


CREATE TABLE Orders (
  OrderID INT PRIMARY KEY,
  ProductID INT,
  Description VARCHAR(255),
  Price DECIMAL(10, 2),
  CustomerName VARCHAR(255),
  StoreID INT,
  SupplierID INT,
  SupplyDate DATE,
  FOREIGN KEY (ProductID) REFERENCES Product(ProductID),
  FOREIGN KEY (StoreID) REFERENCES Store(StoreID),
  FOREIGN KEY (SupplierID) REFERENCES Supplier(SupplierID)
);