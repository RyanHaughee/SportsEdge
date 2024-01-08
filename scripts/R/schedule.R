# include init script
source('init.R')

# Check to see if packages are installed. # Install them if they are not
check.packages <- function(pkg){
  new.pkg <- pkg[!(pkg %in% installed.packages()[, "Package"])]
  if (length(new.pkg))
    install.packages(new.pkg, dependencies = TRUE)
  sapply(pkg, require, character.only = TRUE, quietly = TRUE)
}
packages<-c("Rtools","tidyverse","ggrepel","nflreadr","nflfastR","nflplotR","gsisdecoder")
check.packages(packages)

# Library packages
library(tidyverse)
library(dplyr)
library(readr)
library(stringr)
library(ggrepel)
library(nflreadr)
library(nflfastR)
library(nflplotR)
library(gsisdecoder)

# get connection to platform database
con <- get_db()
db_table <- "nfl_schedule" #UPDATE

# NFL team schedules
nfl_schedule <- load_schedules(seasons = 2018:2023)

# Enable local infile loading for the RMySQL connection
dbGetQuery(con, "SET GLOBAL local_infile = 'ON'")

# Truncate the table
dbExecute(con, paste("TRUNCATE TABLE", db_table))

# Use dbWriteTable to insert the entire data frame
dbWriteTable(
  conn = con,
  name = db_table,
  value = nfl_schedule, # UPDATE
  append = TRUE,  # Append data to the existing table
  overwrite = FALSE,  # Do not overwrite the table
  row.names = FALSE,  # Do not include row names as a separate column
  convert = "NULL"  # Specify how NA values should be handled
)

# Filter non-numeric column names
non_numeric_cols <- names(nfl_schedule)[!sapply(nfl_schedule, is.numeric)] # UPDATE

# SQL query to update empty strings and NA values to NULL for each non-numeric column
for (col in non_numeric_cols) {
  query <- sprintf("UPDATE %s SET %s = NULL WHERE %s = '' OR %s IS NULL;", db_table, col, col, col)
  dbExecute(con, query)
}

# Close the connection
dbDisconnect(con)









