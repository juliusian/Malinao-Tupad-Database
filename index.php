<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DOLE TUPAD</title>
    <link rel="stylesheet" href="styles.css"><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</head>
<body>
    
<h1>MALINAO TUPAD DATABASE</h1>

    
    <div class="search-container">
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-controx xl" placeholder="Search">
            <buttons id="searchButton" class="btn btn-primary mt-2">Search</button>
        </div>
    </div>
    <div id="tableContentSearch" class="hidden">
        <h2>Search Results</h2>
        <table id="contentTableSearch" class="table table-striped">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Extension Name</th>
                    <th>Birthdate</th>
                    <th>Street/ Zone No.</th>
                    <th>Brgy.</th>
                    <th>Type of ID</th>
                    <th>ID No.</th>
                    <th>Contact No.</th>
                    <th>Type of Beneficiary</th>
                    <th>Occupation</th>
                    <th>Sex</th>
                    <th>Civil Status</th>
                    <th>Age</th>
                    <th>Average Monthly Income</th>
                    <th>Dependent</th>
                    <th>Employment Interest</th>
                    <th>Skills Training Needed</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows inserted here by JavaScript -->
            </tbody>
        </table>
    </div>



    <form id="tableForm" method="post" action="save_table.php">
        <label for="tableNameInput">Table Name:</label>
        <input type="text" id="tableNameInput" name="table_name" placeholder="Enter table name" required>

        <table id="dynamicTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Extension Name</th>
                    <th>Birthdate</th>
                    <th>Street/ Zone No.</th>
                    <th>Brgy.</th>
                    <th>Type of ID</th>
                    <th>ID No.</th>
                    <th>Contact No.</th>
                    <th>Type of Beneficiary</th>
                    <th>Occupation</th>
                    <th>Sex</th>
                    <th>Civil Status</th>
                    <th>Age</th>
                    <th>Average Monthly Income</th>
                    <th>Dependent</th>
                    <th>Employment Interest</th>
                    <th>Skills Training Needed</th>
                    <th>Action</th> <!-- For Edit/Delete -->
                </tr>
            </thead>
            <tbody>
                <!-- Rows added dynamically will go here -->
            </tbody>
        </table>
        <input type="hidden" name="table_data" id="tableData">
        <button class="btn" type="button" onclick="addRow()">Add Row</button>
        <button class="btn" type="submit">Save Table</button>
    </form>

    <button class="btn" onclick="toggleView()">See All Tables</button>



    <!-- Section to display saved tables -->
    <div id="savedTables" class="hidden">
        <h2>Saved Tables</h2>
        <div id="tableContainer"></div>
        <div id="tableContentSaved" class="hidden">
        <h2 id="tableNameHeading">Table Content</h2>
            <table id="contentTableSaved" class="table table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>First Name</th>
                        <th>Middle Name</th>
                        <th>Last Name</th>
                        <th>Extension Name</th>
                        <th>Birthdate</th>
                        <th>Street/ Zone No.</th>
                        <th>Brgy.</th>
                        <th>Type of ID</th>
                        <th>ID No.</th>
                        <th>Contact No.</th>
                        <th>Type of Beneficiary</th>
                        <th>Occupation</th>
                        <th>Sex</th>
                        <th>Civil Status</th>
                        <th>Age</th>
                        <th>Average Monthly Income</th>
                        <th>Dependent</th>
                        <th>Employment Interest</th>
                        <th>Skills Training Needed</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows inserted here by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
    

    <script>
        let rowCount = 0;

        function addRow() {
            const table = document.getElementById('dynamicTable').getElementsByTagName('tbody')[0];
            const newRow = table.insertRow();
            for (let i = 0; i < 20; i++) {
                const newCell = newRow.insertCell(i);
                newCell.contentEditable = 'true'; 
                newCell.innerHTML = '';
            }
            const actionCell = newRow.insertCell(20);
            actionCell.innerHTML = `<button class="btn delete-btn" onclick="deleteRow(this)">Delete</button>`;
        }

        function deleteRow(button) {
            const row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }











        document.getElementById('tableForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the default form submission

            const table = document.getElementById('dynamicTable');
            const rows = table.getElementsByTagName('tbody')[0].rows;

            let tableData = [];
            for (let i = 0; i < rows.length; i++) {
                let rowData = [];
                for (let j = 0; j < 20; j++) {
                    let cellValue = rows[i].cells[j].innerText.trim();
                    if (cellValue === "") {
                        alert('Please fill all fields before saving.');
                        return;
                    }
                    rowData.push(cellValue);
                }
                tableData.push(rowData);
            }

            const tableNameInput = document.getElementById('tableNameInput');
            if (tableNameInput.value.trim() === '') {
                alert("Please enter a table name.");
                return;
            }

            fetch('save_table.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    table_name: tableNameInput.value.trim(),
                    table_data: JSON.stringify(tableData)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else if (data.success) {
                    alert(data.success);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
        });













        function toggleView() {
            const savedTablesDiv = document.getElementById('savedTables');
            savedTablesDiv.classList.toggle('hidden');

            if (!savedTablesDiv.classList.contains('hidden')) {
                displaySavedTables();
            }
        }

        function displaySavedTables() {
            fetch('retrieve_tables.php')
            .then(response => response.json())
            .then(data => {
                const tableContainer = document.getElementById('tableContainer');
                tableContainer.innerHTML = '';

                if (data.error) {
                    tableContainer.innerHTML = `<p>Error: ${data.error}</p>`;
                    return;
                }

                if (data.message) {
                    tableContainer.innerHTML = `<p>${data.message}</p>`;
                    return;
                }

                if (!Array.isArray(data)) {
                    tableContainer.innerHTML = '<p>Unexpected data format.</p>';
                    return;
                }

                const tableList = document.createElement('ul');
                data.forEach(tableName => {
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `<a href="#" onclick="showTableContent('${tableName}')">${tableName}</a>`;
                    tableList.appendChild(listItem);
                });
                tableContainer.appendChild(tableList);
            })
            .catch(error => console.error('Error fetching tables:', error));
        }








        function showTableContent(tableName) {
        fetch(`retrieve_table_content.php?table=${tableName}`)
            .then(response => response.json())
            .then(data => {
                // Populate the saved table content
                const tbody = document.getElementById('contentTableSaved').getElementsByTagName('tbody')[0];
                tbody.innerHTML = '';

                data.forEach(row => {
                    const newRow = tbody.insertRow();
                    Object.values(row).forEach(cellValue => {
                        const newCell = newRow.insertCell();
                        newCell.textContent = cellValue;
                    });
                });

                document.getElementById('tableContentSaved').classList.remove('hidden');
            })
            .catch(error => console.error('Error fetching table content:', error));
    }






        document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('searchButton').addEventListener('click', () => {
            const searchTerm = document.getElementById('searchInput').value.trim();

            if (!searchTerm) {
                alert('Please enter a search term.');
                return;
            }

            fetch(`search.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok.');
                    }
                    return response.text(); // Get the response as text first
                })
                .then(text => {
                    try {
                        const data = JSON.parse(text); // Attempt to parse the text as JSON
                        
                        const tbody = document.getElementById('contentTableSearch').getElementsByTagName('tbody')[0];
                        tbody.innerHTML = ''; // Clear previous results

                        if (data.error) {
                            alert(`Error: ${data.error}`);
                            return;
                        }

                        if (!Array.isArray(data)) {
                            alert('Unexpected data format.');
                            return;
                        }

                        if (data.length === 0) {
                            alert('No results found.');
                        } else {
                            data.forEach(row => {
                                const newRow = tbody.insertRow();
                                Object.values(row).forEach(cellValue => {
                                    const newCell = newRow.insertCell();
                                    newCell.textContent = cellValue;
                                });
                            });

                            // Remove hidden class to show the table
                            document.getElementById('tableContentSearch').classList.remove('hidden');
                        }
                    } catch (e) {
                        console.error('Error parsing JSON:', e);
                        console.log('Response Text:', text); // Log the response text for debugging
                        alert('An error occurred while parsing search results.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                    alert('An error occurred while fetching search results.');
                });
        });
    });




        
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
