<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book Tracker</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 2rem;
      background-color: #f9f9f9;
    }
    .card {
      background-color: #fff;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      margin-bottom: 2rem;
      text-align: center;
    }
    h1 {
      margin: 0;
      font-size: 2rem;
    }
    h2 {
      margin: 0.5rem 0 0;
      font-weight: normal;
      font-size: 1.2rem;
      color: #555;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
    }
    th, td {
      padding: 0.75rem;
      border: 1px solid #ddd;
    }
    th {
      background-color: #f2f2f2;
      text-align: left;
    }
  </style>
  <!-- Modal -->
<div id="image-modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background-color:rgba(0,0,0,0.8); text-align:center;">
  <span onclick="closeModal()" style="color:white; position:absolute; top:20px; right:40px; font-size:40px; font-weight:bold; cursor:pointer;">&times;</span>
  <img id="modal-image" style="max-width:80%; max-height:80%; margin-top:60px; border:4px solid white;">
</div>

</head>
<body>

  <div class="card">
    <h1>Book Tracker</h1>
    <h2>by Žiga Terzič</h2>
  </div>

  <table id="book-table">
    <thead>
      <tr>
        <th>Cover</th>
        <th>ISBN</th>
        <th>Title</th>
        <th>Author</th>
        <th>Year Published</th>
      </tr>
    </thead>
    <tbody>
      <!-- Rows will be added by JS -->
    </tbody>
  </table>

  <script>
  window.addEventListener('DOMContentLoaded', async () => {
    try {
      const response = await fetch('/api/index.php');
      if (!response.ok) throw new Error("API error");
      const books = await response.json();

      const tbody = document.querySelector('#book-table tbody');
      books.forEach(book => {
        const row = document.createElement('tr');
        const imageUrl = `https://book-tracker-images-zigat.s3.eu-central-1.amazonaws.com/images/${book.isbn}.png`;
        row.innerHTML = `
          <td>
            <img src="${imageUrl}" width="60" height="90" style="cursor: pointer;" onclick="showImageModal('${imageUrl}')">
          </td>
          <td>${book.isbn}</td>
          <td>${book.title}</td>
          <td>${book.author}</td>
          <td>${book.year_published ?? ''}</td>
        `;
        tbody.appendChild(row);
      });
    } catch (error) {
      alert("Failed to load book data.");
      console.error("Fetch error:", error);
    }
  });

  // Modal image viewer
  function showImageModal(src) {
    const modal = document.getElementById('image-modal');
    const modalImg = document.getElementById('modal-image');
    modalImg.src = src;
    modal.style.display = 'block';
  }

  function closeModal() {
    document.getElementById('image-modal').style.display = 'none';
  }
</script>

</body>
</html>
