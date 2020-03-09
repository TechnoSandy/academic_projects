import java.io.DataInput;
import java.io.DataOutput;
import java.io.IOException;
import java.util.Scanner;
import java.util.Vector;

import org.apache.hadoop.fs.Path;
import org.apache.hadoop.io.DoubleWritable;
import org.apache.hadoop.io.IntWritable;
import org.apache.hadoop.io.Text;
import org.apache.hadoop.io.Writable;
import org.apache.hadoop.io.WritableComparable;
import org.apache.hadoop.mapreduce.Job;
import org.apache.hadoop.mapreduce.Mapper;
import org.apache.hadoop.mapreduce.Reducer;
import org.apache.hadoop.mapreduce.lib.input.FileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.MultipleInputs;
import org.apache.hadoop.mapreduce.lib.input.SequenceFileInputFormat;
import org.apache.hadoop.mapreduce.lib.input.TextInputFormat;
import org.apache.hadoop.mapreduce.lib.output.FileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.SequenceFileOutputFormat;
import org.apache.hadoop.mapreduce.lib.output.TextOutputFormat;

class Elem implements Writable {
	public short tag;
	public int index;
	public double value;

	Elem() {

	}

	Elem(short t, int r, double v) {
		tag = t;
		index = r;
		value = v;
	}

	@Override
	public void write(DataOutput out) throws IOException {

		out.writeShort(tag);
		out.writeInt(index);
		out.writeDouble(value);

	}

	@Override
	public void readFields(DataInput in) throws IOException {

		tag = in.readShort();
		index = in.readInt();
		value = in.readDouble();

	}

}

class Pair implements WritableComparable<Pair> {

	public int i;
	public int j;

	Pair() {

	}

	Pair(int i, int j) {
		this.i = i;
		this.j = j;
	}

	@Override
	public void write(DataOutput out) throws IOException {
		out.writeInt(i);
		out.writeInt(j);

	}

	@Override
	public void readFields(DataInput in) throws IOException {
		i = in.readInt();
		j = in.readInt();
	}

	@Override
	public String toString() {
		return i + " " + j;
	}
	
	@Override
	public int compareTo(Pair p) {
		return i == p.i ? j - p.j : i - p.i;

	}

}

public class Multiply {

	public static class Mapper1 extends Mapper<Object, Text, IntWritable, Elem> {
		private Scanner scanner;

		@Override
		public void map(Object key, Text value, Context context) throws IOException, InterruptedException {
			scanner = new Scanner(value.toString());
			Scanner s = scanner.useDelimiter(",");
			int i = s.nextInt();
			int j = s.nextInt();
			double val = s.nextDouble();
			context.write(new IntWritable(j), new Elem((short) 0, i, val));
			s.close();
		}

	}

	public static class Mapper2 extends Mapper<Object, Text, IntWritable, Elem> {
		private Scanner scanner;

		@Override
		public void map(Object key, Text value, Context context) throws IOException, InterruptedException {
			scanner = new Scanner(value.toString());
			Scanner s = scanner.useDelimiter(",");
			int j = s.nextInt();
			int k = s.nextInt();
			double val = s.nextDouble();
			context.write(new IntWritable(j), new Elem((short) 1, k, val));
			s.close();
		}

	}

	public static class Reducer1 extends Reducer<IntWritable, Elem, Pair, DoubleWritable> {
		static Vector<Elem> M_Matrix = new Vector<>();
		static Vector<Elem> N_Matrix = new Vector<>();

		@Override
		public void reduce(IntWritable key, Iterable<Elem> values, Context context)
				throws IOException, InterruptedException {
			M_Matrix.clear();
			N_Matrix.clear();
			for (Elem element : values) {
				if (element.tag == 0) {
					M_Matrix.add(new Elem(element.tag, element.index, element.value));

				}
				if (element.tag == 1) {
					N_Matrix.add(new Elem(element.tag, element.index, element.value));

				}
			}

			for (Elem M : M_Matrix) {
				for (Elem N : N_Matrix) {
					context.write(new Pair(M.index, N.index), new DoubleWritable(M.value * N.value));

				}
			}

		}
	}

	public static class Mapper3 extends Mapper<Pair, DoubleWritable, Pair, DoubleWritable> {
		@Override
		public void map(Pair p, DoubleWritable value, Context context) throws IOException, InterruptedException {
			context.write(p, value);
		}

	}

	public static class Reducer2 extends Reducer<Pair, DoubleWritable, Pair, DoubleWritable> {

		@Override
		public void reduce(Pair p, Iterable<DoubleWritable> values, Context context)
				throws IOException, InterruptedException {
			double m = 0.0;
			for (DoubleWritable v : values) {
				m = m + v.get();
			}
			context.write(p, new DoubleWritable(m));

		}
	}

	public static void main(String[] args)
			throws IllegalArgumentException, IOException, ClassNotFoundException, InterruptedException {

		Job job1 = Job.getInstance();
		job1.setJobName("job1");
		job1.setJarByClass(Multiply.class);
		job1.setOutputKeyClass(Pair.class);
		job1.setOutputValueClass(DoubleWritable.class);
		job1.setMapOutputKeyClass(IntWritable.class);
		job1.setMapOutputValueClass(Elem.class);
		job1.setReducerClass(Reducer1.class);
		MultipleInputs.addInputPath(job1, new Path(args[0]), TextInputFormat.class, Mapper1.class);
		MultipleInputs.addInputPath(job1, new Path(args[1]), TextInputFormat.class, Mapper2.class);
		job1.setOutputFormatClass(TextOutputFormat.class);
		job1.setOutputFormatClass(SequenceFileOutputFormat.class);
		Path intermediate = new Path(args[2]);
		FileOutputFormat.setOutputPath(job1, intermediate);
		job1.waitForCompletion(true);

		Job job2 = Job.getInstance();
		job2.setJobName("job2");
		job2.setJarByClass(Multiply.class);
		job2.setMapOutputKeyClass(Pair.class);
		job2.setMapOutputValueClass(DoubleWritable.class);
		job2.setOutputKeyClass(Pair.class);
		job2.setOutputValueClass(DoubleWritable.class);
		job2.setInputFormatClass(SequenceFileInputFormat.class);
		job2.setOutputFormatClass(TextOutputFormat.class);
		job2.setMapperClass(Mapper3.class);
		job2.setReducerClass(Reducer2.class);
		FileInputFormat.setInputPaths(job2, intermediate);
		FileOutputFormat.setOutputPath(job2, new Path(args[3]));
		job2.waitForCompletion(true);

	}

}
